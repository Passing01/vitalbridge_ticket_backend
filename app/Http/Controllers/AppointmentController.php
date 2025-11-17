<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Afficher le formulaire de création de rendez-vous
     */
    public function create(Request $request)
    {
        // Récupérer le médecin et la date depuis la requête
        $doctor = User::findOrFail($request->doctor);
        $date = $request->date ? Carbon::parse($request->date) : now();
        
        // Récupérer les créneaux disponibles pour ce médecin
        $availableSlots = $doctor->getAvailableSlots($date);
        
        // Récupérer les patients (pour sélection dans le back-office)
        $patients = User::where('role', 'patient')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('appointments.create', [
            'doctor' => $doctor,
            'date' => $date,
            'availableSlots' => $availableSlots,
            'patients' => $patients,
        ]);
    }

    /**
     * Créer un rendez-vous depuis le back-office (centre de santé / réception)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'patient_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date|after:yesterday',
            'is_urgent' => 'sometimes|boolean',
            'new_patient_first_name' => 'nullable|required_without:patient_id|string|max:255',
            'new_patient_last_name' => 'nullable|required_without:patient_id|string|max:255',
            'new_patient_phone' => 'nullable|required_without:patient_id|string|max:20',
            'new_patient_email' => 'nullable|email|max:255',
        ]);

        $receptionist = Auth::user();
        $doctor = User::findOrFail($validated['doctor_id']);

        if (!empty($validated['patient_id'])) {
            $patient = User::findOrFail($validated['patient_id']);
        } else {
            $password = Str::random(12);

            $patient = User::create([
                'first_name' => $validated['new_patient_first_name'],
                'last_name' => $validated['new_patient_last_name'],
                'phone' => $validated['new_patient_phone'],
                'email' => $validated['new_patient_email'] ?? null,
                'password' => Hash::make($password),
                'role' => 'patient',
                'is_active' => false,
            ]);
        }

        $appointmentDate = Carbon::parse($validated['appointment_date'])->startOfDay();
        $dayOfWeek = strtolower($appointmentDate->englishDayOfWeek);

        // Vérifier si le médecin consulte ce jour-là
        $isWorkingDay = $doctor->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->exists();

        if (!$isWorkingDay) {
            return back()->with('error', "Le médecin ne consulte pas ce jour-là.");
        }

        // Vérifier si le médecin est en congé ce jour-là
        $isOnLeave = $doctor->unavailabilities()
            ->whereDate('unavailable_date', $appointmentDate->toDateString())
            ->where(function ($query) {
                $query->whereNull('start_time')
                    ->orWhereNull('end_time');
            })
            ->exists();

        if ($isOnLeave) {
            return back()->with('error', 'Le médecin est en congé ce jour-là.');
        }

        // Récupérer le profil du médecin avec sa spécialité et le département
        $doctorProfile = $doctor->doctorProfile()->with('specialty.department')->first();

        if (!$doctorProfile || !$doctorProfile->specialty || !$doctorProfile->specialty->department) {
            return back()->with('error', 'Le profil du médecin est incomplet (département ou spécialité manquant).');
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
            'is_urgent' => $request->input('is_urgent', false),
        ]);

        $appointment->save();

        // Gestion de la position dans la file d'attente (même logique que l'API)
        if ($appointment->is_urgent) {
            $appointment->queue_position = 1;

            $doctor->doctorAppointments()
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('id', '!=', $appointment->id)
                ->increment('queue_position');
        } else {
            $lastPosition = $doctor->doctorAppointments()
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('id', '!=', $appointment->id)
                ->max('queue_position');

            $appointment->queue_position = $lastPosition ? $lastPosition + 1 : 1;
        }

        $appointment->save();

        return back()->with('success', 'Rendez-vous créé avec succès.');
    }
    /**
     * Démarrer un rendez-vous
     */
    public function start(Appointment $appointment)
    {
        // Vérifier que l'utilisateur est autorisé à démarrer ce rendez-vous
        // $this->authorize('update', $appointment);
        
        // Vérifier que le rendez-vous n'est pas déjà commencé
        if ($appointment->is_being_served) {
            return redirect()->back()->with('warning', 'Ce rendez-vous est déjà en cours.');
        }
        
        // Vérifier qu'aucun autre rendez-vous n'est en cours pour ce médecin
        $hasOngoingAppointment = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('is_being_served', true)
            ->where('id', '!=', $appointment->id)
            ->exists();
            
        if ($hasOngoingAppointment) {
            return redirect()->back()->with('warning', 'Un autre rendez-vous est déjà en cours pour ce médecin.');
        }
        
        // Mettre à jour le rendez-vous
        $appointment->update([
            'is_being_served' => true,
            'start_time' => now(),
            'status' => 'in_progress'
        ]);
        
        return redirect()->back()->with('success', 'Le rendez-vous a bien été démarré.');
    }
    
    /**
     * Marquer un patient comme absent
     */
    public function markAsAbsent(Appointment $appointment)
    {
        // Vérifier que l'utilisateur est autorisé à marquer ce rendez-vous
        // $this->authorize('update', $appointment);
        
        // Mettre à jour le rendez-vous
        $appointment->update([
            'is_absent' => true,
            'is_being_served' => false,
            'status' => 'cancelled',
            'end_time' => now()
        ]);
        
        return redirect()->back()->with('success', 'Le patient a été marqué comme absent.');
    }
    
    /**
     * Marquer un patient comme présent
     */
    public function markAsPresent(Appointment $appointment)
    {
        // Vérifier que l'utilisateur est autorisé à modifier ce rendez-vous
        // $this->authorize('update', $appointment);
        
        // Mettre à jour le rendez-vous
        $appointment->update([
            'is_absent' => false,
            'status' => 'scheduled'
        ]);
        
        return redirect()->back()->with('success', 'Le patient a été marqué comme présent.');
    }
    
    /**
     * Terminer un rendez-vous
     */
    public function end(Appointment $appointment)
    {
        // Vérifier que l'utilisateur est autorisé à terminer ce rendez-vous
        // $this->authorize('update', $appointment);
        
        // Vérifier que le rendez-vous est bien en cours
        if (!$appointment->is_being_served) {
            return redirect()->back()->with('warning', 'Ce rendez-vous n\'est pas en cours.');
        }
        
        // Mettre à jour le rendez-vous
        $appointment->update([
            'is_being_served' => false,
            'status' => 'completed',
            'end_time' => now()
        ]);
        
        return redirect()->back()->with('success', 'Le rendez-vous a été marqué comme terminé.');
    }
}
