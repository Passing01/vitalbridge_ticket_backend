<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Page "Mes Patients" pour un centre de santé / réception
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->date ? Carbon::parse($request->date)->startOfDay() : now()->startOfDay();

        // Base query : rendez-vous gérés par ce centre (reception_id)
        $baseQuery = Appointment::where('reception_id', $user->id)
            ->with(['patient', 'doctor', 'specialty']);

        // Médecins du centre (avec leur spécialité) pour les filtres
        $doctors = User::where('role', 'doctor')
            ->whereHas('doctorProfile.specialty.department', function ($q) use ($user) {
                $q->where('reception_id', $user->id);
            })
            ->with('doctorProfile.specialty')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $specialties = $doctors->pluck('doctorProfile.specialty')->filter()->unique('id')->values();

        // Rendez-vous du jour, en attente dans la file
        $todayPending = (clone $baseQuery)
            ->whereDate('appointment_date', $date->toDateString())
            ->pending()
            ->orderBy('is_urgent', 'desc')
            ->orderBy('queue_position')
            ->get();

        // Rendez-vous actuellement en consultation
        $current = (clone $baseQuery)
            ->whereDate('appointment_date', $date->toDateString())
            ->where('is_being_served', true)
            ->where('status', 'scheduled')
            ->with('patient')
            ->first();

        // Rendez-vous passés (déjà servis ou date antérieure)
        $pastAppointments = (clone $baseQuery)
            ->where(function ($q) use ($date) {
                $q->whereDate('appointment_date', '<', $date->toDateString())
                  ->orWhere('status', 'completed');
            })
            ->orderBy('appointment_date', 'desc')
            ->limit(100)
            ->get();

        // Toutes les demandes (rendez-vous programmés, futurs ou du jour, non annulés)
        $allRequests = (clone $baseQuery)
            ->whereIn('status', ['scheduled', 'completed'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('patients.index', [
            'date' => $date,
            'todayPending' => $todayPending,
            'current' => $current,
            'pastAppointments' => $pastAppointments,
            'allRequests' => $allRequests,
            'doctors' => $doctors,
            'specialties' => $specialties,
        ]);
    }
}
