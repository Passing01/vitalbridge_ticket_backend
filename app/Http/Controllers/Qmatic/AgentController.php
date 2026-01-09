<?php

namespace App\Http\Controllers\Qmatic;

use App\Http\Controllers\Controller;
use App\Models\QmaticCounter;
use App\Models\QmaticService;
use App\Models\QmaticTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Tableau de bord de l'agent
     */
    public function dashboard()
    {
        $user = Auth::guard('qmatic')->user();
        $healthCenterId = $user->health_center_id;

        // Obtenir le guichet actuel de l'agent
        $counter = QmaticCounter::where('health_center_id', $healthCenterId)
                                ->where('current_agent_id', $user->id)
                                ->where('is_active', true)
                                ->first();

        // Si l'agent n'a pas de guichet assigné, afficher la page de sélection
        if (!$counter) {
            $availableCounters = QmaticCounter::where('health_center_id', $healthCenterId)
                                              ->where('is_active', true)
                                              ->whereNull('current_agent_id')
                                              ->get();

            return view('qmatic.agent.select-counter', compact('availableCounters'));
        }

        // Obtenir le ticket actuel en cours
        $currentTicket = $counter->currentTicket();

        // Obtenir les services supportés par ce guichet
        $serviceIds = $counter->service_ids ?? QmaticService::where('health_center_id', $healthCenterId)
                                                             ->pluck('id')
                                                             ->toArray();

        // Obtenir la file d'attente
        $waitingTickets = QmaticTicket::where('health_center_id', $healthCenterId)
                                      ->whereIn('service_id', $serviceIds)
                                      ->where('status', 'waiting')
                                      ->with('service')
                                      ->orderByPriority()
                                      ->take(20)
                                      ->get();

        // Statistiques du jour
        $todayStats = $this->getTodayStats($user->id);

        return view('qmatic.agent.dashboard', compact(
            'counter',
            'currentTicket',
            'waitingTickets',
            'todayStats'
        ));
    }

    /**
     * Assigner un guichet à l'agent
     */
    public function assignCounter(Request $request)
    {
        $validated = $request->validate([
            'counter_id' => 'required|uuid|exists:qmatic_counters,id',
        ]);

        $user = Auth::guard('qmatic')->user();
        $counter = QmaticCounter::findOrFail($validated['counter_id']);

        // Vérifier que le guichet appartient au même centre
        if ($counter->health_center_id !== $user->health_center_id) {
            return back()->withErrors(['error' => 'Guichet invalide.']);
        }

        // Vérifier que le guichet est disponible
        if ($counter->current_agent_id) {
            return back()->withErrors(['error' => 'Ce guichet est déjà occupé.']);
        }

        // Assigner le guichet
        $counter->assignAgent($user);

        return redirect()->route('qmatic.agent.dashboard')
                        ->with('success', "Guichet {$counter->code} assigné avec succès.");
    }

    /**
     * Libérer le guichet
     */
    public function releaseCounter()
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if ($counter) {
            // Vérifier qu'il n'y a pas de ticket en cours
            $currentTicket = $counter->currentTicket();
            if ($currentTicket) {
                return back()->withErrors(['error' => 'Veuillez terminer le ticket en cours avant de libérer le guichet.']);
            }

            $counter->releaseAgent();

            return redirect()->route('qmatic.agent.dashboard')
                            ->with('success', 'Guichet libéré avec succès.');
        }

        return back();
    }

    /**
     * Appeler le prochain ticket
     */
    public function callNext(Request $request)
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if (!$counter) {
            return back()->withErrors(['error' => 'Aucun guichet assigné.']);
        }

        // Vérifier qu'il n'y a pas déjà un ticket en cours
        $currentTicket = $counter->currentTicket();
        if ($currentTicket) {
            return back()->withErrors(['error' => 'Veuillez terminer le ticket actuel avant d\'appeler le suivant.']);
        }

        // Services supportés
        $serviceIds = $counter->service_ids ?? QmaticService::where('health_center_id', $user->health_center_id)
                                                             ->pluck('id')
                                                             ->toArray();

        // Obtenir le prochain ticket
        $nextTicket = QmaticTicket::where('health_center_id', $user->health_center_id)
                                  ->whereIn('service_id', $serviceIds)
                                  ->where('status', 'waiting')
                                  ->orderByPriority()
                                  ->first();

        if (!$nextTicket) {
            return back()->with('info', 'Aucun ticket en attente.');
        }

        // Appeler le ticket
        $nextTicket->call($counter, $user);

        // Émettre un événement pour l'affichage public (WebSocket)
        // event(new TicketCalledEvent($nextTicket));

        return redirect()->route('qmatic.agent.dashboard')
                        ->with('success', "Ticket {$nextTicket->ticket_number} appelé.");
    }

    /**
     * Rappeler le ticket actuel
     */
    public function recall()
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if (!$counter) {
            return back()->withErrors(['error' => 'Aucun guichet assigné.']);
        }

        $currentTicket = $counter->currentTicket();

        if (!$currentTicket) {
            return back()->withErrors(['error' => 'Aucun ticket à rappeler.']);
        }

        // Utiliser la méthode recall du modèle
        $currentTicket->recall();

        return back()->with('success', "Ticket {$currentTicket->ticket_number} rappelé.");
    }

    /**
     * Démarrer le service du ticket
     */
    public function startServing()
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if (!$counter) {
            return back()->withErrors(['error' => 'Aucun guichet assigné.']);
        }

        $currentTicket = $counter->currentTicket();

        if (!$currentTicket) {
            return back()->withErrors(['error' => 'Aucun ticket en cours.']);
        }

        if ($currentTicket->status === 'serving') {
            return back()->with('info', 'Ce ticket est déjà en cours de service.');
        }

        $currentTicket->startServing();

        return back()->with('success', "Service du ticket {$currentTicket->ticket_number} démarré.");
    }

    /**
     * Marquer le ticket comme servi
     */
    public function markAsServed()
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if (!$counter) {
            return back()->withErrors(['error' => 'Aucun guichet assigné.']);
        }

        $currentTicket = $counter->currentTicket();

        if (!$currentTicket) {
            return back()->withErrors(['error' => 'Aucun ticket en cours.']);
        }

        $currentTicket->markAsServed();

        return redirect()->route('qmatic.agent.dashboard')
                        ->with('success', "Ticket {$currentTicket->ticket_number} marqué comme servi.");
    }

    /**
     * Marquer le ticket comme absent
     */
    public function markAsAbsent()
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if (!$counter) {
            return back()->withErrors(['error' => 'Aucun guichet assigné.']);
        }

        $currentTicket = $counter->currentTicket();

        if (!$currentTicket) {
            return back()->withErrors(['error' => 'Aucun ticket en cours.']);
        }

        $currentTicket->markAsAbsent();

        return redirect()->route('qmatic.agent.dashboard')
                        ->with('success', "Ticket {$currentTicket->ticket_number} marqué comme absent.");
    }

    /**
     * Remettre le ticket en file d'attente
     */
    public function requeue()
    {
        $user = Auth::guard('qmatic')->user();

        $counter = QmaticCounter::where('current_agent_id', $user->id)->first();

        if (!$counter) {
            return back()->withErrors(['error' => 'Aucun guichet assigné.']);
        }

        $currentTicket = $counter->currentTicket();

        if (!$currentTicket) {
            return back()->withErrors(['error' => 'Aucun ticket en cours.']);
        }

        $currentTicket->requeue();

        return redirect()->route('qmatic.agent.dashboard')
                        ->with('success', "Ticket {$currentTicket->ticket_number} remis en file d'attente.");
    }

    /**
     * Obtenir les statistiques de la journée
     */
    private function getTodayStats($agentId)
    {
        $today = now()->startOfDay();

        return [
            'served' => QmaticTicket::where('agent_id', $agentId)
                                    ->where('status', 'served')
                                    ->whereDate('completed_at', $today)
                                    ->count(),
            'absent' => QmaticTicket::where('agent_id', $agentId)
                                    ->where('status', 'absent')
                                    ->whereDate('completed_at', $today)
                                    ->count(),
            'avg_service_time' => QmaticTicket::where('agent_id', $agentId)
                                              ->where('status', 'served')
                                              ->whereDate('completed_at', $today)
                                              ->avg('service_time'),
        ];
    }
}
