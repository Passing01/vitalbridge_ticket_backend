<?php

namespace App\Http\Controllers\Qmatic;

use App\Http\Controllers\Controller;
use App\Models\QmaticService;
use App\Models\QmaticTicket;
use App\Models\QmaticSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KioskController extends Controller
{
    /**
     * Afficher l'interface de prise de ticket (borne)
     */
    public function index(Request $request)
    {
        $healthCenterId = $request->input('center_id') 
                         ?? session('health_center_id')
                         ?? (Auth::check() ? Auth::user()->health_center_id : null);

        // Fallback pour la démo: prendre le premier centre qui a des services
        if (!$healthCenterId) {
            $firstService = QmaticService::first();
            if ($firstService) {
                $healthCenterId = $firstService->health_center_id;
            }
        }

        if ($healthCenterId) {
            session(['health_center_id' => $healthCenterId]);
        }
        
        $services = QmaticService::where('health_center_id', $healthCenterId)
                                 ->where('is_active', true)
                                 ->orderBy('priority_order')
                                 ->orderBy('code')
                                 ->get();

        $settings = [
            'name' => QmaticSetting::get($healthCenterId, 'structure_name', 'VitalBridge Qmatic'),
            'logo' => QmaticSetting::get($healthCenterId, 'structure_logo'),
            'color' => QmaticSetting::get($healthCenterId, 'primary_color', '#2563eb'),
            'welcome' => QmaticSetting::get($healthCenterId, 'welcome_message', 'Bienvenue'),
            'bg_color' => QmaticSetting::get($healthCenterId, 'kiosk_bg_color', '#f3f4f6'),
            'card_bg_color' => QmaticSetting::get($healthCenterId, 'kiosk_card_bg_color', '#ffffff'),
            'text_color' => QmaticSetting::get($healthCenterId, 'kiosk_text_color', '#111827'),
            'layout' => QmaticSetting::get($healthCenterId, 'kiosk_layout', 'grid'),
        ];

        return view('qmatic.kiosk.index', compact('services', 'settings'));
    }

    /**
     * Générer un nouveau ticket
     */
    public function generateTicket(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|uuid|exists:qmatic_services,id',
            'priority' => 'nullable|in:normal,senior,vip,urgent',
        ]);

        $service = QmaticService::findOrFail($validated['service_id']);

        // Vérifier si le service est actif
        if (!$service->is_active) {
            return back()->withErrors(['error' => 'Ce service n\'est pas disponible actuellement.']);
        }

        // Vérifier les horaires d'ouverture
        if (!$service->isOpenAt(now())) {
            return back()->withErrors(['error' => 'Ce service n\'est pas ouvert à cette heure.']);
        }

        // Obtenir le prochain numéro de ticket
        $ticketNumber = $service->getNextTicketNumber();
        
        // Extraire le numéro de séquence
        $sequenceNumber = (int) substr($ticketNumber, strlen($service->code));

        // Créer le ticket
        $ticket = QmaticTicket::create([
            'id' => Str::uuid(),
            'health_center_id' => $service->health_center_id,
            'service_id' => $service->id,
            'ticket_number' => $ticketNumber,
            'sequence_number' => $sequenceNumber,
            'priority' => $validated['priority'] ?? 'normal',
            'status' => 'waiting',
        ]);

        return view('qmatic.kiosk.ticket', compact('ticket'));
    }

    /**
     * Afficher un ticket généré
     */
    public function showTicket(QmaticTicket $ticket)
    {
        return view('qmatic.kiosk.ticket', compact('ticket'));
    }

    /**
     * Vérifier le statut d'un ticket (AJAX)
     */
    public function checkTicketStatus(QmaticTicket $ticket)
    {
        return response()->json([
            'ticket_number' => $ticket->ticket_number,
            'status' => $ticket->status,
            'position' => $this->getTicketPosition($ticket),
            'estimated_wait_time' => $this->getEstimatedWaitTime($ticket),
        ]);
    }

    /**
     * Obtenir la position du ticket dans la file
     */
    private function getTicketPosition(QmaticTicket $ticket): int
    {
        if ($ticket->status !== 'waiting') {
            return 0;
        }

        return QmaticTicket::where('service_id', $ticket->service_id)
                          ->where('status', 'waiting')
                          ->orderByPriority()
                          ->get()
                          ->search(function($t) use ($ticket) {
                              return $t->id === $ticket->id;
                          }) + 1;
    }

    /**
     * Obtenir le temps d'attente estimé
     */
    private function getEstimatedWaitTime(QmaticTicket $ticket): ?int
    {
        if ($ticket->status !== 'waiting') {
            return null;
        }

        // Calculer le temps d'attente moyen des derniers tickets servis
        $avgServiceTime = QmaticTicket::where('service_id', $ticket->service_id)
                                      ->where('status', 'served')
                                      ->whereNotNull('service_time')
                                      ->whereDate('created_at', today())
                                      ->avg('service_time');

        if (!$avgServiceTime) {
            $avgServiceTime = 5; // Temps par défaut: 5 minutes
        }

        $position = $this->getTicketPosition($ticket);

        return round($position * $avgServiceTime);
    }
}
