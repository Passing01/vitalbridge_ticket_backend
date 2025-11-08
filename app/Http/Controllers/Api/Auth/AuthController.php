<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected OTPService $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register a new patient and send OTP
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users',
                'regex:/^(\+?[0-9]{8,15}|0[0-9]{8,15}|[0-9]{8,15})$/'
            ],
            'language' => 'sometimes|in:fr,moore,dioula,english',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'role' => 'patient',
            'language' => $request->language ?? 'fr',
        ]);

        // Generate and send OTP
        $this->otpService->generateAndSendOTP($user);

        // Create a temporary token for OTP verification
        $token = $user->createToken('otp-verification', ['verify-otp']);

        return response()->json([
            'message' => 'Un code de vérification a été envoyé à votre numéro',
            'verification_required' => true,
            'phone' => $user->phone,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Send OTP to user's phone for login
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendOTP(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^(\+?[0-9]{8,15}|0[0-9]{8,15}|[0-9]{8,15})$/'
            ],
        ]);

        $user = User::where('phone', $request->phone)
            ->whereIn('role', ['patient', 'doctor'])
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Aucun compte trouvé avec ce numéro de téléphone',
            ], 404);
        }

        // Generate and send OTP
        $this->otpService->generateAndSendOTP($user);

        // Create a temporary token for OTP verification
        $token = $user->createToken('otp-verification', ['verify-otp']);

        return response()->json([
            'message' => 'Un code de vérification a été envoyé à votre numéro',
            'verification_required' => true,
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'phone' => $user->phone
        ]);
    }

    /**
     * Verify OTP and authenticate user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOTP(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$this->otpService->verifyOTP($user, $request->otp)) {
            return response()->json([
                'message' => 'Code OTP invalide ou expiré',
            ], 401);
        }

        // Mark OTP as verified
        $user->update(['otp_verified_at' => now()]);

        // Créer un nouveau token d'accès complet
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->only(['id', 'first_name', 'last_name', 'phone', 'role'])
        ]);
    }

    /**
     * Get the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Charger les relations en fonction du rôle
        if ($user->role === 'doctor') {
            $user->load([
                'doctorProfile.specialty.department',
                'schedules' => function($query) {
                    $query->where('is_active', true);
                },
                'unavailabilities' => function($query) {
                    $query->where('end_datetime', '>', now())
                          ->orderBy('start_datetime');
                },
                'doctorDelays' => function($query) {
                    $query->where('is_active', true)
                          ->orderBy('created_at', 'desc')
                          ->first();
                }
            ]);

            return response()->json([
                'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'phone', 'role']),
                'profile' => $user->doctorProfile,
                'schedules' => $user->schedules,
                'current_unavailabilities' => $user->unavailabilities,
                'current_delay' => $user->doctorDelays->first()
            ]);
        }

        // Pour les patients, retourner seulement les informations de base
        return response()->json([
            'user' => $user->only(['id', 'first_name', 'last_name', 'phone', 'role'])
        ]);
    }

    /**
     * Logout user (revoke token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
