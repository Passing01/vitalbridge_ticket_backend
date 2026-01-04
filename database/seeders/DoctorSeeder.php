<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer toutes les spécialités
        $specialties = Specialty::all();
        
        // Tableau des préfixes de noms de médecins pour plus de réalisme
        $firstNames = [
            'Jean', 'Pierre', 'Marie', 'Sophie', 'Thomas', 'Nicolas', 'Julie', 'Camille',
            'Alexandre', 'David', 'Laura', 'Emma', 'Lucas', 'Hugo', 'Chloé', 'Sarah',
            'Mohamed', 'Aminata', 'Fatou', 'Moussa', 'Aïssatou', 'Ibrahim', 'Aïcha', 'Oumar'
        ];
        
        $lastNames = [
            'Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand',
            'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia', 'David'
        ];
        
        foreach ($specialties as $specialty) {
            // Créer 1 médecin par spécialité
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName[0] . $lastName . '@example.com');
            
            // Créer l'utilisateur médecin
            $user = User::create([
                'id' => (string) Str::uuid(),
                'first_name' => 'Dr. ' . $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make('password'),
                'phone' => '+225' . rand(10000000, 99999999),
                'role' => 'doctor',
                'otp' => '000000',
                'otp_verified_at' => now(),
                'otp_expires_at' => now()->addHour(),
                'language' => 'fr',
                'is_active' => true,
            ]);

            // Créer le profil du médecin
            $profile = $user->doctorProfile()->create([
                'specialty_id' => $specialty->id,
                'qualification' => fake()->randomElement(['MD', 'PhD', 'Prof', 'Dr', 'Pr', 'Dr.']),
                'bio' => 'Médecin spécialisé en ' . $specialty->name . ' avec une expérience de ' . rand(5, 30) . ' ans.',
                'max_patients_per_day' => rand(10, 30),
                'average_consultation_time' => rand(15, 60),
                'is_available' => rand(0, 1) === 1, // 50% de chance d'être disponible
            ]);

            // Créer les créneaux horaires avec des modèles variés
            // Chaque modèle garantit au moins 4 heures par jour
            $schedulePatterns = [
                // Journée complète classique (8h)
                [
                    'monday' => ['start' => '08:00:00', 'end' => '16:00:00'],
                    'tuesday' => ['start' => '08:00:00', 'end' => '16:00:00'],
                    'wednesday' => ['start' => '08:00:00', 'end' => '16:00:00'],
                    'thursday' => ['start' => '08:00:00', 'end' => '16:00:00'],
                    'friday' => ['start' => '08:00:00', 'end' => '16:00:00'],
                ],
                // Horaires matinaux étendus (7h)
                [
                    'monday' => ['start' => '07:00:00', 'end' => '14:00:00'],
                    'tuesday' => ['start' => '07:00:00', 'end' => '14:00:00'],
                    'wednesday' => ['start' => '07:00:00', 'end' => '14:00:00'],
                    'thursday' => ['start' => '07:00:00', 'end' => '14:00:00'],
                    'friday' => ['start' => '07:00:00', 'end' => '14:00:00'],
                    'saturday' => ['start' => '08:00:00', 'end' => '12:00:00'],
                ],
                // Après-midi et soirée (6h)
                [
                    'monday' => ['start' => '13:00:00', 'end' => '19:00:00'],
                    'tuesday' => ['start' => '13:00:00', 'end' => '19:00:00'],
                    'wednesday' => ['start' => '13:00:00', 'end' => '19:00:00'],
                    'thursday' => ['start' => '13:00:00', 'end' => '19:00:00'],
                    'friday' => ['start' => '13:00:00', 'end' => '19:00:00'],
                ],
                // Journée longue avec samedi (9h en semaine, 5h samedi)
                [
                    'monday' => ['start' => '08:00:00', 'end' => '17:00:00'],
                    'tuesday' => ['start' => '08:00:00', 'end' => '17:00:00'],
                    'wednesday' => ['start' => '08:00:00', 'end' => '17:00:00'],
                    'thursday' => ['start' => '08:00:00', 'end' => '17:00:00'],
                    'friday' => ['start' => '08:00:00', 'end' => '17:00:00'],
                    'saturday' => ['start' => '08:00:00', 'end' => '13:00:00'],
                ],
                // Horaires flexibles (minimum 4h par jour)
                [
                    'monday' => ['start' => '09:00:00', 'end' => '13:00:00'],
                    'tuesday' => ['start' => '14:00:00', 'end' => '18:00:00'],
                    'wednesday' => ['start' => '09:00:00', 'end' => '13:00:00'],
                    'thursday' => ['start' => '14:00:00', 'end' => '18:00:00'],
                    'friday' => ['start' => '08:00:00', 'end' => '12:00:00'],
                    'saturday' => ['start' => '09:00:00', 'end' => '13:00:00'],
                ],
                // Horaires continus (8h30)
                [
                    'monday' => ['start' => '08:30:00', 'end' => '17:00:00'],
                    'tuesday' => ['start' => '08:30:00', 'end' => '17:00:00'],
                    'wednesday' => ['start' => '08:30:00', 'end' => '17:00:00'],
                    'thursday' => ['start' => '08:30:00', 'end' => '17:00:00'],
                    'friday' => ['start' => '08:30:00', 'end' => '17:00:00'],
                ],
            ];

            // Sélectionner un modèle d'horaire aléatoire pour chaque médecin
            $weeklySchedule = $schedulePatterns[array_rand($schedulePatterns)];

            foreach ($weeklySchedule as $day => $hours) {
                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_id' => $user->id,
                        'doctor_profile_id' => $profile->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => $hours['start'],
                        'end_time' => $hours['end'],
                        'is_available' => true,
                    ]
                );
            }
        }
    }
    /**
     * Retourne une qualification aléatoire
     */
    private function getRandomQualification(): string
    {
        $qualifications = [
            'Docteur en Médecine',
            'Spécialiste',
            'Professeur',
            'Chirurgien',
            'Médecin Généraliste',
            'Chef de Service',
            'Praticien Hospitalier',
            'Assistant Hospitalo-Universitaire',
            'Médecin Spécialiste',
            'Médecin-Chef'
        ];
        
        return $qualifications[array_rand($qualifications)];
    }
}
