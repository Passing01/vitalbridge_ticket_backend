<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer tous les utilisateurs avec le rôle 'reception' (centres de santé)
        $healthCenters = User::where('role', 'reception')->get();

        $departments = [
            'Médecine Générale',
            'Pédiatrie',
            'Chirurgie',
            'Gynécologie-Obstétrique',
            'Urgences'
        ];

        foreach ($healthCenters as $center) {
            // Créer 5 départements par centre de santé
            foreach ($departments as $index => $departmentName) {
                Department::create([
                    'name' => $departmentName,
                    'description' => "Service de $departmentName du centre " . $center->name,
                    'reception_id' => $center->id,
                ]);
            }
        }
    }
}
