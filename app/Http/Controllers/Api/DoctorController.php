<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    /**
     * Afficher les détails complets d'un médecin
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // Charger d'abord le médecin avec les relations de base
        $doctor = User::with([
            'doctorProfile.specialty.department',
            'schedules' => function($query) {
                $query->where('is_available', true)
                      ->select(['id', 'doctor_id', 'day_of_week', 'start_time', 'end_time']);
            },
            'doctorDelays' => function($query) {
                $query->select(['id', 'doctor_id', 'delay_duration', 'reason', 'delay_start as started_at'])
                      ->where('delay_start', '>=', now())
                      ->orderBy('delay_start', 'desc')
                      ->first();
            },
            'unavailabilities' => function($query) {
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
            }
        ])
        ->where('users.role', 'doctor')
        ->findOrFail($id);

        // Calculer la disponibilité actuelle du médecin
        $isAvailable = $this->checkDoctorAvailability($doctor);
        
        $doctorData = array_merge(
            $doctor->toArray(),
            ['current_availability' => $isAvailable]
        );

        return response()->json([
            'doctor' => $doctorData
        ]);
    }

    /**
     * Vérifier la disponibilité actuelle du médecin
     *
     * @param User $doctor
     * @return array
     */
    private function checkDoctorAvailability(User $doctor): array
    {
        $now = now();
        $currentDay = strtolower($now->englishDayOfWeek);
        $currentTime = $now->format('H:i:s');
        $currentDate = $now->toDateString();
        
        // Vérifier si le médecin a un profil et est disponible
        $isProfileAvailable = $doctor->doctorProfile && $doctor->doctorProfile->is_available;
        
        // Vérifier les horaires du jour
        $isWithinWorkingHours = false;
        $scheduleToday = $doctor->schedules
            ->where('day_of_week', $currentDay)
            ->where('is_available', true)
            ->first();
        
        if ($scheduleToday) {
            $isWithinWorkingHours = $currentTime >= $scheduleToday->start_time && 
                                  $currentTime <= $scheduleToday->end_time;
        }
        
        // Vérifier les indisponibilités actuelles
        $currentUnavailability = $doctor->unavailabilities
            ->where('unavailable_date', $currentDate)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();
        
        // Vérifier les retards
        $currentDelay = $doctor->doctorDelays->first();
        
        return [
            'is_available' => $isProfileAvailable && 
                             $isWithinWorkingHours && 
                             !$currentUnavailability,
            'reason' => !$doctor->doctorProfile->is_available ? 'Le médecin n\'est pas disponible pour le moment' :
                       (!$isWithinWorkingHours ? 'Hors des heures de consultation' :
                       ($currentUnavailability ? 'Indisponible jusqu\'à ' . $currentUnavailability->end_datetime->format('H:i \l\e d/m/Y') :
                       ($currentDelay ? 'Retard de ' . $currentDelay->delay_minutes . ' minutes' : 'Disponible'))),
            'next_available_slot' => $this->calculateNextAvailableSlot($doctor, $now)
        ];
    }
    
    /**
     * Calculer le prochain créneau disponible
     *
     * @param User $doctor
     * @param \Carbon\Carbon $now
     * @return string|null
     */
    private function calculateNextAvailableSlot(User $doctor, $now)
    {
        // Implémentation simplifiée - à adapter selon les besoins
        $nextAvailable = null;
        
        // Vérifier s'il y a des créneaux disponibles aujourd'hui
        $todaySchedule = $doctor->schedules
            ->where('day_of_week', strtolower($now->englishDayOfWeek))
            ->where('is_available', true)
            ->first();
            
        if ($todaySchedule && $now->format('H:i:s') < $todaySchedule->end_time) {
            // Si on est dans les heures d'ouverture aujourd'hui
            $nextAvailable = $now->format('Y-m-d H:i:s');
        } else {
            // Sinon, trouver le prochain jour d'ouverture
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $currentDayIndex = array_search(strtolower($now->englishDayOfWeek), $days);
            
            for ($i = 1; $i <= 7; $i++) {
                $nextDayIndex = ($currentDayIndex + $i) % 7;
                $nextDay = $days[$nextDayIndex];
                
                $nextDaySchedule = $doctor->schedules
                    ->where('day_of_week', $nextDay)
                    ->where('is_available', true)
                    ->first();
                    
                if ($nextDaySchedule) {
                    $nextDate = $now->copy()->addDays($i)->startOfDay();
                    $nextAvailable = $nextDate->format('Y-m-d') . ' ' . $nextDaySchedule->start_time;
                    break;
                }
            }
        }
        
        return $nextAvailable;
    }
}
