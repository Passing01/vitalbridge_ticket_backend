<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création d'un utilisateur admin
        User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '+225070000000',
            'role' => 'admin',
            'otp' => '000000',
            'otp_expires_at' => now()->addHour(),
            'otp_verified_at' => now(),
            'language' => 'fr',
            'is_active' => true,
        ]);
        
        // Exécution des seeders dans l'ordre
        $this->call([
            HealthCenterSeeder::class,
            DepartmentSeeder::class,
            SpecialtySeeder::class,
            DoctorSeeder::class,
            DoctorSchedulesSeeder::class,
        ]);
        
        // Création d'un utilisateur test
        User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone' => '+225071111111',
            'role' => 'patient',
            'otp' => '123456',
            'otp_expires_at' => now()->addHour(),
            'otp_verified_at' => now(),
            'language' => 'fr',
            'is_active' => true,
        ]);
    }
}
