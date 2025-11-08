<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            // Médecine Générale
            'Médecine Générale' => [
                'Médecine de famille',
                'Médecine interne générale',
                'Médecine d\'urgence',
                'Médecine du travail',
                'Médecine préventive',
            ],
            // Cardiologie
            'Cardiologie' => [
                'Cardiologie interventionnelle',
                'Rythmologie',
                'Cardiologie pédiatrique',
                'Insuffisance cardiaque',
                'Imagerie cardiaque',
            ],
            // Pédiatrie
            'Pédiatrie' => [
                'Néonatologie',
                'Pédiatrie générale',
                'Pédiatrie sociale',
                'Pédiatrie d\'urgence',
                'Pédiatrie néonatale',
            ],
            // Gynécologie-Obstétrique
            'Gynécologie-Obstétrique' => [
                'Échographie gynécologique',
                'Suivi de grossesse',
                'Stérilité et assistance médicale à la procréation',
                'Ménopause',
                'Chirurgie gynécologique',
            ],
            // Chirurgie
            'Chirurgie' => [
                'Chirurgie digestive',
                'Chirurgie vasculaire',
                'Chirurgie thoracique',
                'Chirurgie pédiatrique',
                'Chirurgie plastique et reconstructrice',
            ],
            // Autres spécialités
            'Radiologie' => [
                'Radiologie interventionnelle',
                'Échographie',
                'IRM',
                'Scintigraphie',
                'Mammographie',
            ],
            'Médecine Interne' => [
                'Médecine interne générale',
                'Maladies infectieuses',
                'Médecine tropicale',
                'Médecine vasculaire',
                'Médecine gériatrique',
            ],
            'Dermatologie' => [
                'Dermatologie esthétique',
                'Dermatologie pédiatrique',
                'Dermatologie chirurgicale',
                'Dermatologie allergologique',
                'Dermatologie oncologique',
            ],
            'Ophtalmologie' => [
                'Chirurgie réfractive',
                'Rétine médicale et chirurgicale',
                'Glaucome',
                'Pédiatrie ophtalmologique',
                'Oculoplastie',
            ],
            'ORL' => [
                'Chirurgie ORL pédiatrique',
                'Chirurgie cervico-faciale',
                'Otologie et neuro-otologie',
                'Rhinologie',
                'Laryngologie et phoniatrie',
            ],
            'Urologie' => [
                'Urologie oncologique',
                'Urologie de la femme',
                'Urologie pédiatrique',
                'Andrologie',
                'Laparoscopie urologique',
            ],
            'Neurologie' => [
                'Neurovasculaire',
                'Épileptologie',
                'Sclérose en plaques',
                'Mouvements anormaux',
                'Neuro-oncologie',
            ],
            'Rhumatologie' => [
                'Rhumatologie inflammatoire',
                'Rhumatologie pédiatrique',
                'Médecine physique et réadaptation',
                'Ostéoporose',
                'Rhumatologie interventionnelle',
            ],
            'Pneumologie' => [
                'Pneumologie interventionnelle',
                'Oncologie thoracique',
                'Médecine du sommeil',
                'Insuffisance respiratoire',
                'Allergologie',
            ],
            'Gastro-entérologie' => [
                'Hépatologie',
                'Endoscopie digestive',
                'Pancréatologie',
                'Maladies inflammatoires chroniques intestinales',
                'Proctologie',
            ],
            'Endocrinologie' => [
                'Diabétologie',
                'Maladies thyroïdiennes',
                'Troubles de la croissance',
                'Métabolisme osseux',
                'Obésité et nutrition',
            ],
            'Néphrologie' => [
                'Dialyse',
                'Transplantation rénale',
                'Hypertension artérielle',
                'Néphrologie pédiatrique',
                'Insuffisance rénale chronique',
            ]
        ];

        // Récupérer tous les départements
        $departments = Department::all();
        
        foreach ($departments as $department) {
            $departmentName = $department->name;
            // Extraire le nom de base du département (sans le préfixe)
            $baseDepartmentName = trim(explode('-', $departmentName)[0]);
            
            if (isset($specialties[$baseDepartmentName])) {
                $departmentSpecialties = $specialties[$baseDepartmentName];
                
                // Créer chaque spécialité pour ce département
                foreach ($departmentSpecialties as $specialtyName) {
                    Specialty::create([
                        'name' => $specialtyName,
                        'description' => "Service de $specialtyName - $departmentName",
                        'department_id' => $department->id,
                    ]);
                }
            } else {
                // Fallback si le département n'est pas dans la liste
                for ($i = 1; $i <= 10; $i++) {
                    Specialty::create([
                        'name' => "Spécialité $i - $departmentName",
                        'description' => "Description de la spécialité $i du département $departmentName",
                        'department_id' => $department->id,
                    ]);
                }
            }
        }
    }
}
