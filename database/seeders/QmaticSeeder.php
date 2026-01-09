<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\QmaticService;
use App\Models\QmaticCounter;
use App\Models\QmaticUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QmaticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer un Centre de Santé / Banque (Tenant Principal)
        // On vérifie s'il existe déjà, sinon on le crée
        $center = User::firstOrCreate(
            ['email' => 'admin@banque-demo.com'],
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'Banque',
                'last_name' => 'Centrale',
                'phone' => '00000000',
                'password' => Hash::make('password'),
                'role' => 'reception', // Rôle admin local
                'is_active' => true,
                'otp' => '000000',
                'otp_expires_at' => now()->addHour(),
                'otp_verified_at' => now(),
                'language' => 'fr',
            ]
        );

        $this->command->info("Centre créé : {$center->first_name} {$center->last_name} (ID: {$center->id})");

        // 2. Créer 5 Services
        $servicesData = [
            [
                'code' => 'A',
                'name' => 'Caisse & Retraits',
                'description' => 'Dépôts, retraits et opérations de caisse courantes',
                'priority_order' => 1,
            ],
            [
                'code' => 'B',
                'name' => 'Ouverture de Compte',
                'description' => 'Nouveaux clients, épargne et comptes courants',
                'priority_order' => 2,
            ],
            [
                'code' => 'C',
                'name' => 'Crédits & Prêts',
                'description' => 'Demandes de prêts, simulations et dossiers crédits',
                'priority_order' => 3,
            ],
            [
                'code' => 'D',
                'name' => 'Service Client',
                'description' => 'Réclamations, informations et gestion de carte',
                'priority_order' => 4,
            ],
            [
                'code' => 'E',
                'name' => 'Opérations Internationales',
                'description' => 'Transferts, change et Western Union',
                'priority_order' => 5,
            ],
        ];

        $defaultWorkingHours = [
            'monday' => ['start' => '08:00', 'end' => '17:00'],
            'tuesday' => ['start' => '08:00', 'end' => '17:00'],
            'wednesday' => ['start' => '08:00', 'end' => '17:00'],
            'thursday' => ['start' => '08:00', 'end' => '17:00'],
            'friday' => ['start' => '08:00', 'end' => '17:00'],
        ];

        foreach ($servicesData as $data) {
            $service = QmaticService::updateOrCreate(
                ['health_center_id' => $center->id, 'code' => $data['code']],
                array_merge($data, [
                    'is_active' => true,
                    'working_hours' => $defaultWorkingHours
                ])
            );
            $this->command->info("Service créé/mis à jour : {$service->name} ({$service->code})");
        }

        // 3. Créer 5 Guichets et 5 Agents
        for ($i = 1; $i <= 5; $i++) {
            // Créer l'agent
            $agentUsername = "agent0{$i}";
            $agent = QmaticUser::firstOrCreate(
                ['username' => $agentUsername],
                [
                    'health_center_id' => $center->id,
                    'name' => "Agent {$i} - " . $servicesData[$i-1]['name'],
                    'password' => Hash::make('password'), // Mot de passe par défaut
                    'role' => 'agent',
                    'is_active' => true,
                ]
            );
            
            $this->command->info("Agent créé : {$agent->name} (Login: {$agentUsername} / Pass: password)");

            // Créer le guichet
            $counterCode = "G0{$i}";
            $counter = QmaticCounter::firstOrCreate(
                ['health_center_id' => $center->id, 'code' => $counterCode],
                [
                    'name' => "Guichet {$i}",
                    'is_active' => true,
                    // Ce guichet peut traiter tous les services par défaut, ou on peut restreindre
                    // 'service_ids' => [$service->id] 
                ]
            );
            
            $this->command->info("Guichet créé : {$counter->name} ({$counter->code})");
        }
        
        $this->command->info("Seeder Qmatic terminé avec succès !");
    }
}
