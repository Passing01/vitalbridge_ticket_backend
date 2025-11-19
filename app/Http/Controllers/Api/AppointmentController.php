<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // Récupérer les créneaux disponibles pour un médecin
    public function getAvailableSlots(User $doctor, Request $request)
    {
        // Supporter la sélection d'un profil de médecin (affiliation)
        $profileId = $request->query('doctor_profile_id') ?? $request->query('profile');
        $profile = null;

        if ($profileId) {
            $profile = $doctor->doctorProfiles()->find($profileId);
        }

        // Si un profil est fourni, utiliser ses schedules/unavailabilities et sa durée de consultation
        if ($profile) {
            $schedules = $profile->schedules()->where('is_available', true)->get();
            $consultationTimeGetter = fn() => $profile->average_consultation_time ?? 30;
            $getExistingAppointments = function ($date) use ($doctor, $profile) {
                return $doctor->doctorAppointments()
                    ->whereDate('appointment_date', $date->toDateString())
                    ->where('specialties_id', $profile->specialty_id)
                    ->whereIn('status', ['scheduled'])
                    ->pluck('appointment_date')
                    ->map(fn($dt) => Carbon::parse($dt)->format('H:i'))
                    ->toArray();
            };
            $getUnavailabilities = function ($date) use ($profile) {
                return $profile->unavailabilities()
                    ->whereDate('unavailable_date', $date->toDateString())
                    ->get()
                    ->map(fn($u) => [
                        'start' => Carbon::parse($u->start_time)->format('H:i'),
                        'end' => Carbon::parse($u->end_time)->format('H:i')
                    ]);
            };
        } else {
            // Fallback: utiliser les schedules liés directement au doctor (compatibilité)
            $schedules = $doctor->schedules()->where('is_available', true)->get();
            $consultationTimeGetter = fn() => ($doctor->doctorProfile->average_consultation_time ?? 30);
            $getExistingAppointments = function ($date) use ($doctor) {
                return $doctor->doctorAppointments()
                    ->whereDate('appointment_date', $date->toDateString())
                    ->whereIn('status', ['scheduled'])
                    ->pluck('appointment_date')
                    ->map(fn($dt) => Carbon::parse($dt)->format('H:i'))
                    ->toArray();
            };
            $getUnavailabilities = function ($date) use ($doctor) {
                return $doctor->unavailabilities()
                    ->whereDate('unavailable_date', $date->toDateString())
                    ->get()
                    ->map(fn($u) => [
                        'start' => Carbon::parse($u->start_time)->format('H:i'),
                        'end' => Carbon::parse($u->end_time)->format('H:i')
                    ]);
            };
        }

        // Si le médecin n'a pas de planning défini
        if ($schedules->isEmpty()) {
            return response()->json([
                'message' => 'Aucun planning défini pour ce médecin',
                'available_slots' => []
            ], 200);
        }

        $availableSlots = collect();

        // Pour chaque jour des 30 prochains jours
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i);
            $dayOfWeek = strtolower($date->englishDayOfWeek);

            // Trouver le planning pour ce jour
            $schedule = $schedules->firstWhere('day_of_week', $dayOfWeek);
            if (!$schedule) continue;

            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $consultationTime = $consultationTimeGetter();

            // Récupérer les rendez-vous existants pour ce jour
            $existingAppointments = $getExistingAppointments($date);

            // Récupérer les indisponibilités pour ce jour
            $unavailabilities = $getUnavailabilities($date);

            $currentTime = $startTime->copy();
            $daySlots = [];

            while ($currentTime->addMinutes($consultationTime)->lte($endTime)) {
                $slotStart = $currentTime->copy()->subMinutes($consultationTime);
                $slotEnd = $currentTime->copy();
                $slotTime = $slotStart->format('H:i');

                // Vérifier si le créneau est disponible
                $isAvailable = !in_array($slotTime, $existingAppointments) &&
                              !$this->isInUnavailability($slotStart, $slotEnd, $unavailabilities);

                if ($isAvailable) {
                    $daySlots[] = [
                        'time' => $slotTime,
                        'formatted_time' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                        'timestamp' => $date->copy()->setTimeFrom($slotStart)->toDateTimeString(),
                        'date' => $date->toDateString(),
                        'day_name' => $date->translatedFormat('l')
                    ];
                }
            }

            if (!empty($daySlots)) {
                $availableSlots->push([
                    'date' => $date->toDateString(),
                    'day_name' => $date->translatedFormat('l'),
                    'slots' => $daySlots
                ]);
            }
        }

        return response()->json([
            'available_slots' => $availableSlots,
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->full_name,
                'specialty' => $profile ? ($profile->specialty->name ?? null) : ($doctor->doctorProfile->specialty->name ?? null),
                'doctor_profile_id' => $profile? $profile->id : null,
            ]
        ]);
    }

    // Vérifier si un créneau est dans une période d'indisponibilité
    private function isInUnavailability($start, $end, $unavailabilities)
    {
        foreach ($unavailabilities as $unavailability) {
            $unavailableStart = Carbon::parse($unavailability['start']);
            $unavailableEnd = Carbon::parse($unavailability['end']);

            if ($start->lt($unavailableEnd) && $end->gt($unavailableStart)) {
                return true;
            }
        }
        return false;
    }
    // Prendre un rendez-vous
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:yesterday',
            'is_urgent' => 'sometimes|boolean',
            'doctor_profile_id' => 'nullable|exists:doctor_profiles,id'
        ]);

        $patient = Auth::user();
        $doctor = User::findOrFail($request->doctor_id);

        $appointmentDate = Carbon::parse($request->appointment_date)->startOfDay();
        $dayOfWeek = strtolower($appointmentDate->englishDayOfWeek);

        // Si un profile est fourni, l'utiliser
        $profile = null;
        if ($request->filled('doctor_profile_id')) {
            $profile = $doctor->doctorProfiles()->find($request->input('doctor_profile_id'));
        }

        // Vérifier si le médecin consulte ce jour-là (par profil si possible)
        if ($profile) {
            $isWorkingDay = $profile->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->exists();
        } else {
            $isWorkingDay = $doctor->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->where('is_available', true)
                ->exists();
        }

        if (!$isWorkingDay) {
            return response()->json([
                'message' => 'Le médecin ne consulte pas ce jour-là.'
            ], 422);
        }

        // Vérifier si le médecin est en congé ce jour-là (par profil si possible)
        if ($profile) {
            $isOnLeave = $profile->unavailabilities()
                ->whereDate('unavailable_date', $appointmentDate->toDateString())
                ->where(function($query) {
                    $query->whereNull('start_time')
                          ->orWhereNull('end_time');
                })
                ->exists();
        } else {
            $isOnLeave = $doctor->unavailabilities()
                ->whereDate('unavailable_date', $appointmentDate->toDateString())
                ->where(function($query) {
                    $query->whereNull('start_time')
                          ->orWhereNull('end_time');
                })
                ->exists();
        }

        if ($isOnLeave) {
            return response()->json([
                'message' => 'Le médecin est en congé ce jour-là.'
            ], 422);
        }

        // Récupérer le profil du médecin: si $profile non défini, prendre le premier
        if (!isset($profile) || !$profile) {
            $profile = $doctor->doctorProfiles()->with('specialty.department')->first();
        } else {
            $profile->load('specialty.department');
        }

        if (!$profile) {
            return response()->json([
                'message' => 'Le profil du médecin est introuvable.'
            ], 400);
        }

        // Vérifier que la spécialité et le département sont bien définis
        if (!$profile->specialty || !$profile->specialty->department) {
            return response()->json([
                'message' => 'Le profil du médecin est incomplet. Département ou spécialité manquant.'
            ], 400);
        }
        
        // Récupérer un réceptionniste du même département
        $receptionist = User::where('role', 'reception')
            ->whereHas('managedDepartments', function($query) use ($profile) {
                $query->where('id', $profile->specialty->department->id);
            })
            ->first();
            
        if (!$receptionist) {
            return response()->json([
                'message' => 'Aucun réceptionniste trouvé pour ce département.'
            ], 400);
        }

        // Créer le rendez-vous
        $appointment = new Appointment([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'reception_id' => $receptionist->id,
            'departments_id' => $profile->specialty->department->id,
            'specialties_id' => $profile->specialty_id,
            'appointment_date' => $request->appointment_date,
            'status' => 'scheduled',
            'is_urgent' => $request->input('is_urgent', false)
        ]);

        // Enregistrer le rendez-vous pour obtenir un ID
        $appointment->save();

        // Si c'est un rendez-vous urgent, on le met en tête de file
        if ($appointment->is_urgent) {
            $appointment->queue_position = 1;
            // Décaller les autres positions
            $doctor->doctorAppointments()
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('id', '!=', $appointment->id)
                ->increment('queue_position');
        } else {
            // Sinon, on le met à la fin de la file
            $lastPosition = $doctor->doctorAppointments()
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('id', '!=', $appointment->id)
                ->max('queue_position');
            
            $appointment->queue_position = $lastPosition ? $lastPosition + 1 : 1;
        }
        
        $appointment->save();

        return response()->json([
            'message' => 'Rendez-vous pris avec succès',
            'appointment' => $appointment->load([
                'doctor', 
                'reception', 
                'department', 
                'specialty',
                'patient'
            ])
        ], 201);
    }

    // Liste des rendez-vous d'un patient
    public function patientAppointments()
    {
        $appointments = Auth::user()->patientAppointments()
            ->with([
                'doctor',
                'reception',
                'department',
                'specialty'
            ])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return response()->json([
            'appointments' => $appointments
        ]);
    }

    // Détails d'un rendez-vous (patient)
    public function showPatientAppointment($id)
    {
        $appointment = Auth::user()->patientAppointments()
            ->with([
                'doctor',
                'reception',
                'department',
                'specialty'
            ])
            ->findOrFail($id);

        return response()->json([
            'appointment' => $appointment
        ]);
    }

    // Liste des rendez-vous d'un médecin
    public function doctorAppointments()
    {
        $appointments = Auth::user()->doctorAppointments()
            ->with([
                'patient',
                'reception',
                'department',
                'specialty'
            ])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return response()->json([
            'appointments' => $appointments
        ]);
    }

    // Détails d'un rendez-vous (médecin)
    public function showDoctorAppointment($id)
    {
        $appointment = Auth::user()->doctorAppointments()
            ->with([
                'patient',
                'reception',
                'department',
                'specialty'
            ])
            ->findOrFail($id);

        return response()->json([
            'appointment' => $appointment
        ]);
    }

    // Annuler un rendez-vous (patient)
    public function cancel($id)
    {
        $appointment = Auth::user()->patientAppointments()
            ->where('id', $id)
            ->where('status', 'scheduled')
            ->firstOrFail();

        $appointment->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Rendez-vous annulé avec succès'
        ]);
    }

    // Marquer un rendez-vous comme terminé (médecin)
    public function complete($id)
    {
        $appointment = Auth::user()->doctorAppointments()
            ->where('id', $id)
            ->where('status', 'scheduled')
            ->firstOrFail();

        $appointment->update(['status' => 'completed']);

        return response()->json([
            'message' => 'Rendez-vous marqué comme terminé'
        ]);
    }
}
