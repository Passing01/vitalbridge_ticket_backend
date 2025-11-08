<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DoctorSchedulesSeeder extends Seeder
{
    public function run()
    {
        // Trouver un médecin
        $doctor = User::where('role', 'doctor')->first();
        
        if (!$doctor) {
            $this->command->info('Aucun médecin trouvé. Créez d\'abord un médecin.');
            return;
        }

        // Jours de la semaine
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        
        // Créer un planning pour chaque jour de la semaine
        foreach ($days as $day) {
            DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day
                ],
                [
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                    'is_available' => true
                ]
            );
        }
        
        $this->command->info("Planning créé pour le Dr. {$doctor->first_name} {$doctor->last_name} (ID: {$doctor->id})");
    }
}
