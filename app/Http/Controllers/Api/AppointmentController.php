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
        // Récupérer les plannings du médecin
        $schedules = $doctor->schedules()
            ->where('is_available', true)
            ->get();

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
            $consultationTime = $doctor->doctorProfile->average_consultation_time ?? 30; // en minutes
            
            // Récupérer les rendez-vous existants pour ce jour
            $existingAppointments = $doctor->doctorAppointments()
                ->whereDate('appointment_date', $date->toDateString())
                ->whereIn('status', ['scheduled'])
                ->pluck('appointment_date')
                ->map(function ($dt) {
                    return Carbon::parse($dt)->format('H:i');
                })
                ->toArray();

            // Récupérer les indisponibilités pour ce jour
            $unavailabilities = $doctor->unavailabilities()
                ->whereDate('unavailable_date', $date->toDateString())
                ->get()
                ->map(function ($unavailability) {
                    return [
                        'start' => Carbon::parse($unavailability->start_time)->format('H:i'),
                        'end' => Carbon::parse($unavailability->end_time)->format('H:i')
                    ];
                });

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
                'specialty' => $doctor->doctorProfile->specialty->name ?? null
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
            'is_urgent' => 'sometimes|boolean'
        ]);

        $patient = Auth::user();
        $doctor = User::findOrFail($request->doctor_id);

        $appointmentDate = Carbon::parse($request->appointment_date)->startOfDay();
        $dayOfWeek = strtolower($appointmentDate->englishDayOfWeek);
        
        // Vérifier si le médecin consulte ce jour-là
        $isWorkingDay = $doctor->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->exists();
            
        if (!$isWorkingDay) {
            return response()->json([
                'message' => 'Le médecin ne consulte pas ce jour-là.'
            ], 422);
        }
        
        // Vérifier si le médecin est en congé ce jour-là
        $isOnLeave = $doctor->unavailabilities()
            ->whereDate('unavailable_date', $appointmentDate->toDateString())
            ->where(function($query) {
                $query->whereNull('start_time')
                      ->orWhereNull('end_time');
            })
            ->exists();
            
        if ($isOnLeave) {
            return response()->json([
                'message' => 'Le médecin est en congé ce jour-là.'
            ], 422);
        }

        // Récupérer le profil du médecin avec sa spécialité et le département
        $doctorProfile = $doctor->doctorProfile()->with('specialty.department')->first();
        
        if (!$doctorProfile) {
            return response()->json([
                'message' => 'Le profil du médecin est introuvable.'
            ], 400);
        }
        
        // Vérifier que la spécialité et le département sont bien définis
        if (!$doctorProfile->specialty || !$doctorProfile->specialty->department) {
            return response()->json([
                'message' => 'Le profil du médecin est incomplet. Département ou spécialité manquant.'
            ], 400);
        }
        
        // Récupérer un réceptionniste du même département
        $receptionist = User::where('role', 'reception')
            ->whereHas('managedDepartments', function($query) use ($doctorProfile) {
                $query->where('id', $doctorProfile->specialty->department->id);
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
            'departments_id' => $doctorProfile->specialty->department->id,
            'specialties_id' => $doctorProfile->specialty_id,
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
