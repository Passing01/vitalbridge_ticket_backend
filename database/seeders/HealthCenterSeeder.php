<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HealthCenterSeeder extends Seeder
{
    public function run(): void
    {
        $healthCenters = [
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'CHU de Yalgado',
                'last_name' => 'Ouedraogo',
                'phone' => '+22625307060',
                'email' => 'contact@chuyo.bf',
                'password' => bcrypt('password'),
                'role' => 'reception',
                'otp' => '000000',
                'otp_verified_at' => now(),
                'otp_expires_at' => now()->addHour(),
                'language' => 'fr',
            ],
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'CHU Pédiatrique',
                'last_name' => 'Charles de Gaulle',
                'phone' => '+22625363600',
                'email' => 'contact@chupcdg.bf',
                'password' => bcrypt('password'),
                'role' => 'reception',
                'otp' => '000000',
                'otp_verified_at' => now(),
                'otp_expires_at' => now()->addHour(),
                'language' => 'fr',
            ],
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'CMA',
                'last_name' => 'SAGA',
                'phone' => '+22625361206',
                'email' => 'contact@cmasaga.bf',
                'password' => bcrypt('password'),
                'role' => 'reception',
                'otp' => '000000',
                'otp_verified_at' => now(),
                'otp_expires_at' => now()->addHour(),
                'language' => 'fr',
            ],
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'Clinique',
                'last_name' => 'El Fateh Suka',
                'phone' => '+22625312789',
                'email' => 'contact@fatehsuka.bf',
                'password' => bcrypt('password'),
                'role' => 'reception',
                'otp' => '000000',
                'otp_verified_at' => now(),
                'otp_expires_at' => now()->addHour(),
                'language' => 'fr',
            ]
        ];

        foreach ($healthCenters as $center) {
            User::create($center);
        }
    }
}
