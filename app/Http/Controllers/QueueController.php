<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    /**
     * Afficher la liste des files d'attente (pour admin/réception)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer la date de la requête ou utiliser aujourd'hui par défaut (sans l'heure)
        $date = $request->date ? \Carbon\Carbon::parse($request->date)->startOfDay() : now()->startOfDay();
        
        // Récupérer le terme de recherche s'il existe
        $search = $request->input('search');
        
        // Si c'est un réceptionniste, ne montrer que les médecins de son département
        if ($user->role === 'reception') {
            $doctors = User::where('role', 'doctor')
                ->whereHas('doctorProfile.specialty.department', function($query) use ($user) {
                    $query->where('reception_id', $user->id);
                })
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhereHas('doctorProfile.specialty', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    });
                })
                ->withCount(['doctorAppointments as pending_count' => function($query) use ($date) {
                    $query->whereDate('appointment_date', $date->format('Y-m-d'))
                          ->where('status', 'scheduled')
                          ->where('is_absent', false);
                }])
                ->with(['doctorAppointments' => function($query) use ($date) {
                    $query->whereDate('appointment_date', $date->format('Y-m-d'))
                          ->where('is_being_served', true)
                          ->with('patient');
                }])
                ->orderBy('last_name')
                ->paginate(10);
        } else {
            // Pour les administrateurs, montrer tous les médecins
            $doctors = User::where('role', 'doctor')
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhereHas('doctorProfile.specialty', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%");
                          });
                    });
                })
                ->withCount(['doctorAppointments as pending_count' => function($query) use ($date) {
                    $query->whereDate('appointment_date', $date->format('Y-m-d'))
                          ->where('status', 'scheduled')
                          ->where('is_absent', false);
                }])
                ->with(['doctorAppointments' => function($query) use ($date) {
                    $query->whereDate('appointment_date', $date->format('Y-m-d'))
                          ->where('is_being_served', true)
                          ->with('patient');
                }])
                ->orderBy('last_name')
                ->paginate(10);
        }

        return view('queues.index', compact('doctors', 'date', 'search'));
    }

    /**
     * Afficher la file d'attente d'un médecin spécifique
     */
    public function show(User $doctor, Request $request, $date = null)
    {
        // $this->authorize('viewQueue', [Appointment::class, $doctor]);
        
        // Récupérer la date de la requête ou utiliser aujourd'hui par défaut (sans l'heure)
        $date = $date ? \Carbon\Carbon::parse($date)->startOfDay() : 
               ($request->date ? \Carbon\Carbon::parse($request->date)->startOfDay() : now()->startOfDay());
        
        $queue = $this->getQueueData($doctor, $date);
        
        // Récupérer les rendez-vous en attente
        $pending = $queue->where('status', 'scheduled')
                        ->where('is_absent', false);
                        
        // Récupérer le patient actuellement en consultation
        $current = $queue->where('is_being_served', true)->first();
        
        // Récupérer les patients marqués comme absents
        $absent = $queue->where('is_absent', true);
        
        // Récupérer les patients déjà servis
        $served = $queue->where('status', 'served');
        
        return view('queues.show', compact(
            'doctor', 'pending', 'current', 'absent', 'served', 'date'
        ));
    }

    /**
     * Appeler un patient
     */
    public function call(Appointment $appointment)
    {
        // $this->authorize('callNext', [Appointment::class, $appointment->doctor]);
        
        DB::transaction(function() use ($appointment) {
            // Marquer le patient comme en cours de consultation
            $appointment->update([
                'is_being_served' => true,
                'called_at' => now(),
                'missed_calls' => 0
            ]);
            
            // Mettre à jour les autres rendez-vous
            $appointment->doctor->doctorAppointments()
                ->where('id', '!=', $appointment->id)
                ->where('is_being_served', true)
                ->update(['is_being_served' => false]);
        });
        
        return back()->with('success', 'Patient appelé avec succès');
    }

    /**
     * Marquer un patient comme servi
     */
    public function serve(Appointment $appointment)
    {
        // $this->authorize('update', $appointment);
        
        $appointment->update([
            'status' => 'served',
            'is_being_served' => false,
            'served_at' => now()
        ]);
        
        return back()->with('success', 'Patient marqué comme servi');
    }

    /**
     * Marquer un patient comme absent
     */
    public function markAsAbsent(Appointment $appointment)
    {
        // $this->authorize('update', $appointment);
        
        $appointment->update([
            'is_absent' => true,
            'is_being_served' => false,
            'missed_calls' => $appointment->missed_calls + 1
        ]);
        
        return back()->with('warning', 'Patient marqué comme absent');
    }

    /**
     * Replacer un patient dans la file d'attente
     */
    public function requeue(Appointment $appointment)
    {
        // $this->authorize('update', $appointment);
        
        $appointment->update([
            'is_absent' => false,
            'status' => 'scheduled',
            'queue_position' => $appointment->doctor->doctorAppointments()
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->max('queue_position') + 1
        ]);
        
        return back()->with('success', 'Patient replacé dans la file d\'attente');
    }

    /**
     * Marquer un rendez-vous comme urgent
     */
    public function markAsUrgent(Appointment $appointment)
    {
        // $this->authorize('update', $appointment);
        
        DB::transaction(function() use ($appointment) {
            // Mettre à jour le statut urgent
            $appointment->update([
                'is_urgent' => !$appointment->is_urgent,
                'queue_position' => $appointment->is_urgent ? 
                    $appointment->doctor->doctorAppointments()
                        ->whereDate('appointment_date', $appointment->appointment_date)
                        ->max('queue_position') + 1 : 1
            ]);
            
            // Si on marque comme urgent, on le place en tête de file
            if (!$appointment->is_urgent) {
                $appointment->doctor->doctorAppointments()
                    ->whereDate('appointment_date', $appointment->appointment_date)
                    ->where('id', '!=', $appointment->id)
                    ->increment('queue_position');
            }
        });
        
        return back()->with('success', 'Statut d\'urgence mis à jour');
    }

    /**
     * Récupérer les données de la file d'attente
     */
    private function getQueueData(User $doctor, $date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date->format('Y-m-d')) : now()->startOfDay();
        
        return $doctor->doctorAppointments()
            ->with(['patient', 'specialty'])
            ->whereDate('appointment_date', $date->toDateString())
            ->orderBy('is_urgent', 'desc')
            ->orderBy('queue_position')
            ->get();
    }
}
