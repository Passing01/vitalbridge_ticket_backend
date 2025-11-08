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
        Schema::table('appointments', function (Blueprint $table) {
            $table->integer('queue_position')->nullable()->after('status');
            $table->boolean('is_urgent')->default(false)->after('queue_position');
            $table->boolean('is_absent')->default(false)->after('is_urgent');
            $table->boolean('is_being_served')->default(false)->after('is_absent');
            $table->timestamp('called_at')->nullable()->after('is_being_served');
            $table->timestamp('served_at')->nullable()->after('called_at');
            $table->integer('missed_calls')->default(0)->after('served_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'queue_position',
                'is_urgent',
                'is_absent',
                'is_being_served',
                'called_at',
                'served_at',
                'missed_calls'
            ]);
        });
    }
};
