<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table des utilisateurs Qmatic (Agents & Admins locaux)
        Schema::create('qmatic_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('health_center_id'); // Lien vers le centre de santé (Tenant)
            $table->string('name');
            $table->string('username')->unique(); // Login par nom d'utilisateur
            $table->string('password');
            $table->enum('role', ['admin', 'agent'])->default('agent');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            
            // On lie au centre de santé principal (table users avec role reception)
            $table->foreign('health_center_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // 2. Table des services Qmatic
        Schema::create('qmatic_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('health_center_id');
            $table->string('code', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('priority_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('working_hours')->nullable();
            $table->timestamps();
            
            $table->foreign('health_center_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->unique(['health_center_id', 'code']);
        });

        // 3. Table des guichets
        Schema::create('qmatic_counters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('health_center_id');
            $table->string('code', 10);
            $table->string('name');
            $table->json('service_ids')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('current_agent_id')->nullable(); // Référence qmatic_users
            $table->timestamps();
            
            $table->foreign('health_center_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('current_agent_id')
                  ->references('id')
                  ->on('qmatic_users') // CHANGÉ: users -> qmatic_users
                  ->onDelete('set null');
            
            $table->unique(['health_center_id', 'code']);
        });

        // 4. Table des tickets
        Schema::create('qmatic_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('health_center_id');
            $table->uuid('service_id');
            $table->string('ticket_number', 20);
            $table->integer('sequence_number');
            $table->enum('priority', ['normal', 'senior', 'vip', 'urgent'])->default('normal');
            $table->enum('status', ['waiting', 'called', 'serving', 'served', 'absent', 'cancelled'])
                  ->default('waiting');
            $table->uuid('counter_id')->nullable();
            $table->uuid('agent_id')->nullable(); // Référence qmatic_users
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('wait_time')->nullable();
            $table->integer('service_time')->nullable();
            $table->timestamps();
            
            $table->foreign('health_center_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('service_id')
                  ->references('id')
                  ->on('qmatic_services')
                  ->onDelete('cascade');
            
            $table->foreign('counter_id')
                  ->references('id')
                  ->on('qmatic_counters')
                  ->onDelete('set null');
            
            $table->foreign('agent_id')
                  ->references('id')
                  ->on('qmatic_users') // CHANGÉ: users -> qmatic_users
                  ->onDelete('set null');
            
            $table->index(['health_center_id', 'service_id', 'status']);
            $table->index(['health_center_id', 'created_at']);
        });

        // 5. Table des appels de tickets
        Schema::create('qmatic_ticket_calls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_id');
            $table->uuid('counter_id');
            $table->uuid('agent_id'); // Référence qmatic_users
            $table->timestamp('called_at');
            $table->timestamps();
            
            $table->foreign('ticket_id')
                  ->references('id')
                  ->on('qmatic_tickets')
                  ->onDelete('cascade');
            
            $table->foreign('counter_id')
                  ->references('id')
                  ->on('qmatic_counters')
                  ->onDelete('cascade');
            
            $table->foreign('agent_id')
                  ->references('id')
                  ->on('qmatic_users') // CHANGÉ: users -> qmatic_users
                  ->onDelete('cascade');
        });

        // 6. Table des paramètres Qmatic
        Schema::create('qmatic_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('health_center_id');
            $table->string('key');
            $table->json('value');
            $table->timestamps();
            
            $table->foreign('health_center_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->unique(['health_center_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qmatic_settings');
        Schema::dropIfExists('qmatic_ticket_calls');
        Schema::dropIfExists('qmatic_tickets');
        Schema::dropIfExists('qmatic_counters');
        Schema::dropIfExists('qmatic_services');
        Schema::dropIfExists('qmatic_users');
    }
};
