<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        
        return view('appointments.create', [
            'doctor' => $doctor,
            'date' => $date,
            'availableSlots' => $availableSlots
        ]);
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
