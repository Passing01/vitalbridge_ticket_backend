<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HealthCenterFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'Centre Hospitalier Universitaire',
            'Centre de Santé Intégré',
            'Polyclinique Les Jasmins',
            'Centre Médical de la Paix',
            'Hôpital Général',
            'Clinique des Spécialités',
            'Centre de Santé Communautaire',
            'Centre Médical Urbain'
        ];

        $name = $this->faker->unique()->randomElement($names) . ' ' . $this->faker->city;
        
        return [
            'name' => $name,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'country' => $this->faker->country,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
