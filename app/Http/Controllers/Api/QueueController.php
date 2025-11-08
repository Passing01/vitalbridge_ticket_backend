<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    /**
     * Afficher la file d'attente du jour pour un médecin
     */
    public function index(User $doctor): JsonResponse
    {
        $this->authorize('viewQueue', [Appointment::class, $doctor]);

        $queue = $doctor->doctorAppointments()
            ->with(['patient', 'specialty'])
            ->whereDate('appointment_date', today())
            ->where('status', 'scheduled')
            ->orderBy('is_urgent', 'desc')
            ->orderBy('queue_position')
            ->get();

        return response()->json([
            'queue' => $queue,
            'current_position' => $this->getCurrentPosition($doctor)
        ]);
    }

    /**
     * Appeler le prochain patient
     */
    public function callNext(User $doctor): JsonResponse
    {
        $this->authorize('callNext', [Appointment::class, $doctor]);

        return DB::transaction(function () use ($doctor) {
            // Trouver le prochain rendez-vous
            $nextAppointment = $doctor->doctorAppointments()
                ->whereDate('appointment_date', today())
                ->where('status', 'scheduled')
                ->where('is_absent', false)
                ->whereNull('served_at')
                ->orderBy('is_urgent', 'desc')
                ->orderBy('queue_position')
                ->first();

            if (!$nextAppointment) {
                return response()->json([
                    'message' => 'Aucun patient en attente.'
                ], 404);
            }

            // Marquer comme appelé
            $nextAppointment->markAsCalled();

            return response()->json([
                'message' => 'Patient appelé avec succès',
                'appointment' => $nextAppointment->load('patient')
            ]);
        });
    }

    /**
     * Marquer un patient comme servi
     */
    public function markAsServed(Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->markAsServed();

        return response()->json([
            'message' => 'Patient marqué comme servi avec succès',
            'appointment' => $appointment->load('patient')
        ]);
    }

    /**
     * Marquer un patient comme absent
     */
    public function markAsAbsent(Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->markAsAbsent();

        return response()->json([
            'message' => 'Patient marqué comme absent',
            'appointment' => $appointment->load('patient')
        ]);
    }

    /**
     * Réinsérer un patient absent dans la file d'attente
     */
    public function requeue(Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        if (!$appointment->is_absent) {
            return response()->json([
                'message' => 'Ce patient n\'est pas marqué comme absent.'
            ], 400);
        }

        $appointment->requeue();

        return response()->json([
            'message' => 'Patient réinséré dans la file d\'attente',
            'appointment' => $appointment->load('patient')
        ]);
    }

    /**
     * Marquer un rendez-vous comme urgent
     */
    public function markAsUrgent(Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->update([
            'is_urgent' => true,
            'queue_position' => 1 // Mettre en tête de file
        ]);

        // Décaller les autres positions
        $appointment->doctor->appointments()
            ->where('id', '!=', $appointment->id)
            ->whereDate('appointment_date', today())
            ->increment('queue_position');

        return response()->json([
            'message' => 'Rendez-vous marqué comme urgent',
            'appointment' => $appointment->load('patient')
        ]);
    }

    /**
     * Obtenir la position actuelle dans la file d'attente
     */
    private function getCurrentPosition(User $doctor): ?int
    {
        return $doctor->doctorAppointments()
            ->whereDate('appointment_date', today())
            ->where('is_being_served', true)
            ->where('status', 'scheduled')
            ->value('queue_position');
    }
}
