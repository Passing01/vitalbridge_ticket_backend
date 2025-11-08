<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $first_name = fake()->firstName();
        $last_name = fake()->lastName();
        
        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => $first_name . ' ' . $last_name,
            'email' => strtolower($first_name . '.' . $last_name . '@example.com'),
            'email_verified_at' => now(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),
            'role' => 'doctor',
            'is_active' => fake()->boolean(90), // 90% de chance d'être actif
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
    
    /**
     * Configure the model factory to create a doctor with a profile.
     */
    public function doctor()
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $user->assignRole('doctor');
            
            // Créer un profil médecin
            $user->doctorProfile()->create([
                'qualification' => $this->faker->randomElement([
                    'Docteur en Médecine',
                    'Spécialiste',
                    'Professeur',
                    'Chirurgien',
                    'Médecin Généraliste'
                ]),
                'bio' => $this->faker->paragraph(),
                'specialty_id' => null, // Sera défini lors de la création
                'is_available' => $this->faker->boolean(80), // 80% de chance d'être disponible
                'max_patients_per_day' => $this->faker->numberBetween(10, 30),
                'average_consultation_time' => $this->faker->numberBetween(15, 45),
            ]);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
