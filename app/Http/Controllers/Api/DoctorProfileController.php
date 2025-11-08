<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorUnavailability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DoctorProfileController extends Controller
{
    /**
     * Récupérer le profil complet du médecin
     *
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $user->load([
            'doctorProfile.specialty.department',
            'schedules',
            'unavailabilities' => function($query) {
                $query->where('end_datetime', '>', now());
            },
            'doctorDelays' => function($query) {
                $query->orderBy('created_at', 'desc')->first();
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

    /**
     * Déclarer un retard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setDelay(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'delay_minutes' => 'required|integer|min:1|max:240', // max 4 heures
            'reason' => 'nullable|string|max:500'
        ]);

        $delay = $user->doctorDelays()->create([
            'delay_minutes' => $validated['delay_minutes'],
            'reason' => $validated['reason'] ?? null,
            'started_at' => now(),
            'is_active' => true
        ]);

        // Désactiver les retards précédents
        $user->doctorDelays()
            ->where('id', '!=', $delay->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return response()->json([
            'message' => 'Retard enregistré avec succès',
            'delay' => $delay
        ]);
    }

    /**
     * Ajouter une indisponibilité
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addUnavailability(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason' => 'required|string|max:500',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_end_date' => 'required_if:is_recurring,true|date|after:end_datetime'
        ]);

        $unavailability = $user->unavailabilities()->create([
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'reason' => $validated['reason'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurrence_pattern' => $validated['is_recurring'] ? $validated['recurrence_pattern'] : null,
            'recurrence_end_date' => $validated['is_recurring'] ? $validated['recurrence_end_date'] : null
        ]);

        return response()->json([
            'message' => 'Indisponibilité enregistrée avec succès',
            'unavailability' => $unavailability
        ]);
    }

    /**
     * Supprimer une indisponibilité
     *
     * @param int $id
     * @return JsonResponse
     */
    public function removeUnavailability($id): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $unavailability = $user->unavailabilities()->findOrFail($id);
        $unavailability->delete();

        return response()->json([
            'message' => 'Indisponibilité supprimée avec succès'
        ]);
    }
}
