<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\DoctorUnavailability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DoctorProfileController extends Controller
{
    /**
     * Récupérer toutes les affiliations du médecin
     *
     * @return JsonResponse
     */
    public function getAffiliations(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $user->load([
            'doctorProfiles.specialty.department',
            'doctorProfiles.schedules',
            'doctorProfiles.unavailabilities' => function($query) {
                $query->where('unavailable_date', '>=', now()->toDateString())
                      ->orderBy('unavailable_date')
                      ->orderBy('start_time');
            },
            'doctorProfiles.delays' => function($query) {
                $query->where('is_active', true)
                      ->where('delay_start', '>=', now())
                      ->orderBy('delay_start', 'desc');
            }
        ]);

        $affiliations = $user->doctorProfiles->map(function($profile) {
            return [
                'id' => $profile->id,
                'specialty' => $profile->specialty ? [
                    'id' => $profile->specialty->id,
                    'name' => $profile->specialty->name,
                    'department' => $profile->specialty->department ? [
                        'id' => $profile->specialty->department->id,
                        'name' => $profile->specialty->department->name,
                        'health_center_id' => $profile->specialty->department->reception_id,
                    ] : null,
                ] : null,
                'qualification' => $profile->qualification,
                'bio' => $profile->bio,
                'max_patients_per_day' => $profile->max_patients_per_day,
                'average_consultation_time' => $profile->average_consultation_time,
                'is_available' => $profile->is_available,
                'schedules' => $profile->schedules,
                'current_unavailabilities' => $profile->unavailabilities,
                'current_delay' => $profile->delays->first(),
            ];
        });

        return response()->json([
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'phone', 'role']),
            'affiliations' => $affiliations,
        ]);
    }

    /**
     * Récupérer le profil complet d'une affiliation spécifique
     *
     * @param int $profileId
     * @return JsonResponse
     */
    public function getProfile($profileId): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        $profile = $user->doctorProfiles()
            ->with([
                'specialty.department',
                'schedules',
                'unavailabilities' => function($query) {
                    $query->where('unavailable_date', '>=', now()->toDateString())
                          ->orderBy('unavailable_date')
                          ->orderBy('start_time');
                },
                'delays' => function($query) {
                    $query->where('is_active', true)
                          ->where('delay_start', '>=', now())
                          ->orderBy('delay_start', 'desc');
                }
            ])
            ->findOrFail($profileId);

        return response()->json([
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'phone', 'role']),
            'profile' => [
                'id' => $profile->id,
                'specialty' => $profile->specialty ? [
                    'id' => $profile->specialty->id,
                    'name' => $profile->specialty->name,
                    'department' => $profile->specialty->department ? [
                        'id' => $profile->specialty->department->id,
                        'name' => $profile->specialty->department->name,
                        'health_center_id' => $profile->specialty->department->reception_id,
                    ] : null,
                ] : null,
                'qualification' => $profile->qualification,
                'bio' => $profile->bio,
                'max_patients_per_day' => $profile->max_patients_per_day,
                'average_consultation_time' => $profile->average_consultation_time,
                'is_available' => $profile->is_available,
                'schedules' => $profile->schedules,
                'current_unavailabilities' => $profile->unavailabilities,
                'current_delay' => $profile->delays->first(),
            ],
        ]);
    }

    /**
     * Déclarer un retard pour une affiliation spécifique
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
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'delay_duration' => 'required|integer|min:1|max:240', // max 4 heures en minutes
            'reason' => 'nullable|string|max:500'
        ]);

        // Vérifier que le profil appartient au médecin
        $profile = $user->doctorProfiles()->findOrFail($validated['doctor_profile_id']);

        $delay = $profile->delays()->create([
            'doctor_id' => $user->id,
            'delay_start' => now(),
            'delay_duration' => $validated['delay_duration'],
            'reason' => $validated['reason'] ?? null,
            'is_active' => true
        ]);

        // Désactiver les retards précédents pour ce profil
        $profile->delays()
            ->where('id', '!=', $delay->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return response()->json([
            'message' => 'Retard enregistré avec succès',
            'delay' => $delay
        ]);
    }

    /**
     * Ajouter une indisponibilité pour une affiliation spécifique
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
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'unavailable_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        // Vérifier que le profil appartient au médecin
        $profile = $user->doctorProfiles()->findOrFail($validated['doctor_profile_id']);

        $unavailability = $profile->unavailabilities()->create([
            'doctor_id' => $user->id,
            'unavailable_date' => $validated['unavailable_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'],
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

        // Vérifier que l'indisponibilité appartient à une affiliation du médecin
        $unavailability = DoctorUnavailability::whereHas('doctorProfile', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($id);

        $unavailability->delete();

        return response()->json([
            'message' => 'Indisponibilité supprimée avec succès'
        ]);
    }
}
