<?php

namespace Database\Seeders;

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
            // Créer 5 médecins par spécialité
            for ($i = 1; $i <= 5; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $email = strtolower($firstName[0] . $lastName . $i . '@example.com');
                
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
                $user->doctorProfile()->create([
                    'specialty_id' => $specialty->id,
                    'qualification' => fake()->randomElement(['MD', 'PhD', 'Prof', 'Dr', 'Pr', 'Dr.']),
                    'bio' => 'Médecin spécialisé en ' . $specialty->name . ' avec une expérience de ' . rand(5, 30) . ' ans.',
                    'max_patients_per_day' => rand(10, 30),
                    'average_consultation_time' => rand(15, 60),
                    'is_available' => rand(0, 1) === 1, // 50% de chance d'être disponible
                ]);
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
