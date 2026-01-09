<?php

namespace App\Http\Controllers\Qmatic;

use App\Http\Controllers\Controller;
use App\Models\QmaticTicket;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    /**
     * Affichage public (écran de salle d'attente)
     */
    public function index(Request $request)
    {
        // ID du centre de santé (peut être passé en paramètre ou via session)
        $healthCenterId = $request->input('health_center_id') 
                         ?? session('health_center_id');

        // Fallback pour la démo
        if (!$healthCenterId) {
            $firstService = \App\Models\QmaticService::first();
            if ($firstService) {
                $healthCenterId = $firstService->health_center_id;
                session(['health_center_id' => $healthCenterId]);
            }
        }

        if (!$healthCenterId) {
            abort(403, 'Centre de santé non spécifié.');
        }

        // Obtenir les derniers tickets appelés (pour affichage)
        $recentCalls = QmaticTicket::where('health_center_id', $healthCenterId)
                                   ->whereIn('status', ['called', 'serving', 'served'])
                                   ->with(['service', 'counter'])
                                   ->orderBy('called_at', 'desc')
                                   ->take(10)
                                   ->get();

        // Obtenir les statistiques globales
        $stats = [
            'waiting' => QmaticTicket::where('health_center_id', $healthCenterId)
                                     ->where('status', 'waiting')
                                     ->count(),
            'serving' => QmaticTicket::where('health_center_id', $healthCenterId)
                                     ->whereIn('status', ['called', 'serving'])
                                     ->count(),
            'served_today' => QmaticTicket::where('health_center_id', $healthCenterId)
                                          ->where('status', 'served')
                                          ->whereDate('completed_at', today())
                                          ->count(),
        ];

        // Obtenir les statistiques par service
        $services = \App\Models\QmaticService::where('health_center_id', $healthCenterId)
                                             ->where('is_active', true)
                                             ->get();
        
        $serviceStats = [];
        foreach ($services as $service) {
            $serviceStats[$service->id] = [
                'name' => $service->name,
                'color' => $service->color,
                'waiting' => QmaticTicket::where('health_center_id', $healthCenterId)
                                        ->where('service_id', $service->id)
                                        ->where('status', 'waiting')
                                        ->count(),
            ];
        }

        $settings = [
            'name' => \App\Models\QmaticSetting::get($healthCenterId, 'structure_name', 'VitalBridge Qmatic'),
            'logo' => \App\Models\QmaticSetting::get($healthCenterId, 'structure_logo'),
            'color' => \App\Models\QmaticSetting::get($healthCenterId, 'primary_color', '#2563eb'),
            'announcement' => \App\Models\QmaticSetting::get($healthCenterId, 'display_announcement', 'Veuillez vous diriger vers le guichet indiqué'),
            'announcement_language' => \App\Models\QmaticSetting::get($healthCenterId, 'announcement_language', 'fr-FR'),
            'announcement_gender' => \App\Models\QmaticSetting::get($healthCenterId, 'announcement_gender', 'female'),
            'announcement_multi_lang' => \App\Models\QmaticSetting::get($healthCenterId, 'announcement_multi_lang', '0'),
            'announcement_template' => \App\Models\QmaticSetting::get($healthCenterId, 'announcement_template', 'Ticket {ticket}, au guichet {counter}'),
            'display_layout' => \App\Models\QmaticSetting::get($healthCenterId, 'display_layout', 'sidebar_right'),
            'display_bg_color' => \App\Models\QmaticSetting::get($healthCenterId, 'display_bg_color', '#111827'),
            'display_secondary_color' => \App\Models\QmaticSetting::get($healthCenterId, 'display_secondary_color', '#1f2937'),
            'display_text_color' => \App\Models\QmaticSetting::get($healthCenterId, 'display_text_color', '#ffffff'),
            'template_moore' => \App\Models\QmaticSetting::get($healthCenterId, 'template_moore', 'Ticket {ticket}, guichet {counter} nênga'),
            'template_dioula' => \App\Models\QmaticSetting::get($healthCenterId, 'template_dioula', 'Ticket {ticket}, ka taga guichet {counter} la'),
        ];

        return view('qmatic.display.index', compact('recentCalls', 'stats', 'healthCenterId', 'settings', 'serviceStats'));
    }

    /**
     * API pour obtenir les mises à jour en temps réel (polling)
     */
    public function updates(Request $request)
    {
        $healthCenterId = $request->input('health_center_id');

        if (!$healthCenterId) {
            return response()->json(['error' => 'health_center_id requis'], 400);
        }

        // Derniers appels (depuis la dernière vérification)
        $since = $request->input('since'); // Timestamp
        
        $query = QmaticTicket::where('health_center_id', $healthCenterId)
                             ->whereIn('status', ['called', 'serving', 'served'])
                             ->with(['service', 'counter']);

        if ($since) {
            $query->where('called_at', '>', $since);
        }

        $recentCalls = $query->orderBy('called_at', 'desc')
                            ->take(10)
                            ->get();

        // Calculer les stats par service
        $services = \App\Models\QmaticService::where('health_center_id', $healthCenterId)
                                             ->where('is_active', true)
                                             ->get();
        
        $serviceStats = [];
        foreach ($services as $service) {
            $serviceStats[$service->id] = [
                'name' => $service->name,
                'color' => $service->color,
                'waiting' => QmaticTicket::where('health_center_id', $healthCenterId)
                                        ->where('service_id', $service->id)
                                        ->where('status', 'waiting')
                                        ->count(),
            ];
        }

        return response()->json([
            'calls' => $recentCalls,
            'timestamp' => now()->toIso8601String(),
            'stats' => [
                'waiting' => QmaticTicket::where('health_center_id', $healthCenterId)
                                         ->where('status', 'waiting')
                                         ->count(),
                'serving' => QmaticTicket::where('health_center_id', $healthCenterId)
                                         ->whereIn('status', ['called', 'serving'])
                                         ->count(),
            ],
            'serviceStats' => $serviceStats,
        ]);
    }

    /**
     * Affichage plein écran
     */
    public function fullscreen(Request $request)
    {
        $healthCenterId = $request->input('health_center_id') 
                         ?? session('health_center_id');

        if (!$healthCenterId) {
            abort(403, 'Centre de santé non spécifié.');
        }

        return view('qmatic.display.fullscreen', compact('healthCenterId'));
    }
}
