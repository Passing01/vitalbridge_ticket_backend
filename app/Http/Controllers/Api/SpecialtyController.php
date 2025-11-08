<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    /**
     * Afficher les détails d'une spécialité avec ses médecins
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // Nettoyer l'ID en enlevant les accolades si présentes
        $cleanId = trim($id, '{}');
        
        // Vérifier si l'ID est numérique
        if (!is_numeric($cleanId)) {
            return response()->json([
                'message' => 'ID de spécialité invalide',
                'requested_id' => $id,
                'clean_id' => $cleanId,
                'available_specialties' => Specialty::select('id', 'name')->get()
            ], 400);
        }
        
        $specialty = Specialty::with([
            'doctorProfiles' => function($query) {
                $query->select([
                    'id',
                    'user_id',
                    'specialty_id',
                    'qualification',
                    'bio',
                    'max_patients_per_day',
                    'average_consultation_time',
                    'is_available'
                ]);
            },
            'doctorProfiles.user' => function($query) {
                $query->select([
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone'
                ]);
            },
            'doctorProfiles.delays' => function($query) {
                $query->select(['id', 'doctor_id', 'delay_duration', 'delay_start as started_at', 'reason']);
            },
            'doctorProfiles.unavailabilities' => function($query) {
                $now = now();
                $currentDate = $now->toDateString();
                $currentTime = $now->format('H:i:s');
                
                $query->where(function($q) use ($currentDate, $currentTime) {
                    // Soit la date est dans le futur
                    $q->where('unavailable_date', '>', $currentDate)
                      // Soit c'est aujourd'hui mais l'heure de fin est dans le futur
                      ->orWhere(function($q2) use ($currentDate, $currentTime) {
                          $q2->where('unavailable_date', $currentDate)
                             ->where('end_time', '>', $currentTime);
                      });
                })
                ->select(['id', 'doctor_id', 'unavailable_date', 'start_time', 'end_time', 'reason'])
                ->orderBy('unavailable_date')
                ->orderBy('start_time');
            },
            'doctorProfiles.schedules' => function($query) {
                $query->where('is_available', true)
                    ->select(['id', 'doctor_id', 'day_of_week', 'start_time', 'end_time'])
                    ->orderBy('day_of_week')
                    ->orderBy('start_time');
            }
        ])
        ->withCount(['doctorProfiles as active_doctors_count' => function($query) {
            $query->whereHas('user', function($q) {
                $q->where('is_active', true);
            });
        }])
        ->find($cleanId);
        
        if (!$specialty) {
            return response()->json([
                'message' => 'Spécialité non trouvée',
                'requested_id' => $cleanId,
                'available_specialties' => Specialty::select('id', 'name')->get()
            ], 404);
        }

        // Formater la réponse
        $response = [
            'id' => $specialty->id,
            'name' => $specialty->name,
            'description' => $specialty->description,
            'active_doctors_count' => $specialty->active_doctors_count,
            'doctors' => $specialty->doctorProfiles->map(function($doctorProfile) {
                return [
                    'id' => $doctorProfile->user_id,
                    'first_name' => $doctorProfile->user->first_name,
                    'last_name' => $doctorProfile->user->last_name,
                    'email' => $doctorProfile->user->email,
                    'phone' => $doctorProfile->user->phone,
                    'qualification' => $doctorProfile->qualification,
                    'bio' => $doctorProfile->bio,
                    'max_patients_per_day' => $doctorProfile->max_patients_per_day,
                    'average_consultation_time' => $doctorProfile->average_consultation_time,
                    'is_available' => $doctorProfile->is_available,
                    'delays' => $doctorProfile->delays,
                    'unavailabilities' => $doctorProfile->unavailabilities,
                    'schedules' => $doctorProfile->schedules
                ];
            })
        ];

        return response()->json([
            'specialty' => $response
        ]);
    }
}
