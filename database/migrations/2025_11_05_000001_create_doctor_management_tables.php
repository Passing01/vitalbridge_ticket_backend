<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des départements
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('reception_id')->nullable();
            $table->timestamps();
            
            // Clé étrangère vers la table users pour le réceptionniste
            $table->foreign('reception_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });

        // Table des spécialités médicales
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Table pour les informations supplémentaires des médecins
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained()->onDelete('cascade');
            $table->string('qualification');
            $table->text('bio')->nullable();
            $table->integer('max_patients_per_day')->default(20);
            $table->integer('average_consultation_time')->default(30); // en minutes
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Table des disponibilités hebdomadaires
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('doctor_id')->constrained('users')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['doctor_id', 'day_of_week']);
        });

        // Table des indisponibilités ponctuelles
        Schema::create('doctor_unavailabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('unavailable_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('reason');
            $table->timestamps();
        });

        // Table des retards
        Schema::create('doctor_delays', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('doctor_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('delay_start');
            $table->integer('delay_duration'); // en minutes
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_delays');
        Schema::dropIfExists('doctor_unavailabilities');
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('doctor_profiles');
        Schema::dropIfExists('specialties');
        Schema::dropIfExists('departments');
    }
};
