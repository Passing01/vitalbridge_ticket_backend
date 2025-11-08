<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Données factices pour le tableau de bord
        $todayTickets = 12;
        $ticketGrowth = 5;
        
        $pendingTickets = 8;
        $pendingGrowth = 2;
        
        $resolvedTickets = 24;
        $resolvedGrowth = 12;
        
        $satisfactionRate = 85;
        $satisfactionGrowth = 2;

        // Données factices pour le graphique des tendances (7 derniers jours)
        $ticketTrends = [
            'labels' => [],
            'data' => [12, 19, 15, 8, 10, 12, 14]
        ];
        
        // Générer les dates des 7 derniers jours
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $ticketTrends['labels'][] = $date->format('d M');
        }

        // Répartition des statuts (en pourcentage)
        $statusDistribution = [
            'open' => 35,
            'in_progress' => 25,
            'resolved' => 40,
        ];

        // Données factices pour les tickets récents
        $recentTickets = collect([
            (object)[
                'id' => 1001,
                'subject' => 'Problème de connexion',
                'status' => 'open',
                'priority' => 'high',
                'updated_at' => now()->subMinutes(30),
            ],
            (object)[
                'id' => 1002,
                'subject' => 'Demande de renseignements',
                'status' => 'in_progress',
                'priority' => 'medium',
                'updated_at' => now()->subHours(2),
            ],
            (object)[
                'id' => 1003,
                'subject' => 'Problème d\'impression',
                'status' => 'resolved',
                'priority' => 'low',
                'updated_at' => now()->subDays(1),
            ],
        ]);
        
        // Simuler une pagination
        $recentTickets = new \Illuminate\Pagination\LengthAwarePaginator(
            $recentTickets,
            $recentTickets->count(),
            10, // Nombre d'éléments par page
            1   // Page courante
        );

        return view('dashboard', [
            'todayTickets' => $todayTickets,
            'ticketGrowth' => $ticketGrowth,
            'pendingTickets' => $pendingTickets,
            'pendingGrowth' => $pendingGrowth,
            'resolvedTickets' => $resolvedTickets,
            'resolvedGrowth' => $resolvedGrowth,
            'satisfactionRate' => $satisfactionRate,
            'satisfactionGrowth' => $satisfactionGrowth,
            'ticketTrends' => $ticketTrends,
            'statusDistribution' => $statusDistribution,
            'recentTickets' => $recentTickets,
        ]);
    }
}
