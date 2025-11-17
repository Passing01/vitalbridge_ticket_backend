<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ajouter un index unique sur (user_id, specialty_id) dans doctor_profiles
        // pour éviter qu'un médecin ait plusieurs profils avec la même spécialité
        // Vérifier d'abord si l'index n'existe pas déjà
        try {
            $indexExists = DB::select(
                "SELECT COUNT(*) as count 
                 FROM information_schema.STATISTICS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'doctor_profiles' 
                 AND INDEX_NAME = 'doctor_profiles_user_specialty_unique'"
            );
            
            if (empty($indexExists) || $indexExists[0]->count == 0) {
                Schema::table('doctor_profiles', function (Blueprint $table) {
                    $table->unique(['user_id', 'specialty_id'], 'doctor_profiles_user_specialty_unique');
                });
            }
        } catch (\Exception $e) {
            // Si l'index existe déjà ou autre erreur, continuer
        }

        // 2. Pour doctor_schedules, on doit d'abord supprimer la contrainte FK sur doctor_id
        // pour pouvoir supprimer l'index unique, puis recréer la FK
        // Récupérer le nom de la contrainte FK (Laravel génère généralement: doctor_schedules_doctor_id_foreign)
        $constraintName = null;
        try {
            $constraints = DB::select(
                "SELECT CONSTRAINT_NAME 
                 FROM information_schema.KEY_COLUMN_USAGE 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'doctor_schedules' 
                 AND COLUMN_NAME = 'doctor_id' 
                 AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            if (!empty($constraints)) {
                $constraintName = $constraints[0]->CONSTRAINT_NAME;
            }
        } catch (\Exception $e) {
            // Si on ne peut pas trouver la contrainte, continuer
        }

        // Supprimer la contrainte FK si elle existe
        if ($constraintName) {
            DB::statement("ALTER TABLE `doctor_schedules` DROP FOREIGN KEY `{$constraintName}`");
        }

        // Supprimer l'index unique (Laravel génère généralement: doctor_schedules_doctor_id_day_of_week_unique)
        try {
            DB::statement("ALTER TABLE `doctor_schedules` DROP INDEX `doctor_schedules_doctor_id_day_of_week_unique`");
        } catch (\Exception $e) {
            // Si l'index n'existe pas avec ce nom, essayer de le trouver dynamiquement
            $indexName = $this->getUniqueIndexName('doctor_schedules', ['doctor_id', 'day_of_week']);
            if ($indexName) {
                DB::statement("ALTER TABLE `doctor_schedules` DROP INDEX `{$indexName}`");
            }
        }

        // Recréer la contrainte FK sur doctor_id si elle a été supprimée
        if ($constraintName) {
            DB::statement("ALTER TABLE `doctor_schedules` ADD CONSTRAINT `{$constraintName}` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
        }

        // 3. Ajouter doctor_profile_id dans doctor_schedules (nullable d'abord pour la migration)
        // Vérifier si la colonne n'existe pas déjà
        if (!$this->columnExists('doctor_schedules', 'doctor_profile_id')) {
            Schema::table('doctor_schedules', function (Blueprint $table) {
                $table->foreignId('doctor_profile_id')->nullable()->after('doctor_id')
                      ->constrained('doctor_profiles')->onDelete('cascade');
            });
        }

        // 4. Ajouter doctor_profile_id dans doctor_unavailabilities
        if (!$this->columnExists('doctor_unavailabilities', 'doctor_profile_id')) {
            Schema::table('doctor_unavailabilities', function (Blueprint $table) {
                $table->foreignId('doctor_profile_id')->nullable()->after('doctor_id')
                      ->constrained('doctor_profiles')->onDelete('cascade');
            });
        }

        // 5. Ajouter doctor_profile_id dans doctor_delays
        if (!$this->columnExists('doctor_delays', 'doctor_profile_id')) {
            Schema::table('doctor_delays', function (Blueprint $table) {
                $table->foreignId('doctor_profile_id')->nullable()->after('doctor_id')
                      ->constrained('doctor_profiles')->onDelete('cascade');
            });
        }
        
        // Ajouter le champ is_active dans doctor_delays si il n'existe pas
        if (!$this->columnExists('doctor_delays', 'is_active')) {
            Schema::table('doctor_delays', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('reason');
            });
        }

        // 6. Migrer les données existantes
        // Associer les schedules/unavailabilities/delays existants au profil du médecin
        $this->migrateExistingData();

        // 7. Rendre doctor_profile_id non nullable dans doctor_schedules après la migration
        // et ajouter l'index unique sur (doctor_profile_id, day_of_week)
        // Vérifier d'abord si la colonne existe et si l'index n'existe pas déjà
        if ($this->columnExists('doctor_schedules', 'doctor_profile_id')) {
            // Vérifier si l'index unique n'existe pas déjà
            $indexExists = DB::select(
                "SELECT COUNT(*) as count 
                 FROM information_schema.STATISTICS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'doctor_schedules' 
                 AND INDEX_NAME = 'doctor_schedules_profile_day_unique'"
            );
            
            if (empty($indexExists) || $indexExists[0]->count == 0) {
                Schema::table('doctor_schedules', function (Blueprint $table) {
                    // Vérifier si la colonne est nullable
                    $columns = DB::select("SHOW COLUMNS FROM `doctor_schedules` WHERE Field = 'doctor_profile_id'");
                    if (!empty($columns) && $columns[0]->Null === 'YES') {
                        // Supprimer temporairement la contrainte de clé étrangère
                        try {
                            $table->dropForeign(['doctor_profile_id']);
                        } catch (\Exception $e) {
                            // La contrainte n'existe peut-être pas, continuer
                        }
                        // Changer la colonne en non nullable
                        $table->foreignId('doctor_profile_id')->nullable(false)->change();
                        // Recréer la contrainte de clé étrangère
                        $table->foreign('doctor_profile_id')->references('id')->on('doctor_profiles')->onDelete('cascade');
                    }
                    // Ajouter l'index unique sur (doctor_profile_id, day_of_week)
                    $table->unique(['doctor_profile_id', 'day_of_week'], 'doctor_schedules_profile_day_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Migrer les données avant de supprimer les colonnes
        // (on associe les données au doctor_id directement)

        // 2. Supprimer doctor_profile_id de doctor_delays
        Schema::table('doctor_delays', function (Blueprint $table) {
            $table->dropForeign(['doctor_profile_id']);
            $table->dropColumn(['doctor_profile_id', 'is_active']);
        });

        // 3. Supprimer doctor_profile_id de doctor_unavailabilities
        Schema::table('doctor_unavailabilities', function (Blueprint $table) {
            $table->dropForeign(['doctor_profile_id']);
            $table->dropColumn('doctor_profile_id');
        });

        // 4. Restaurer l'index unique sur (doctor_id, day_of_week) dans doctor_schedules
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropUnique('doctor_schedules_profile_day_unique');
            $table->dropForeign(['doctor_profile_id']);
            $table->dropColumn('doctor_profile_id');
            // Recréer l'index unique sur (doctor_id, day_of_week)
            // Note: La contrainte FK sur doctor_id devrait déjà exister
            $table->unique(['doctor_id', 'day_of_week']);
        });

        // 5. Supprimer l'index unique sur (user_id, specialty_id) dans doctor_profiles
        Schema::table('doctor_profiles', function (Blueprint $table) {
            $table->dropUnique('doctor_profiles_user_specialty_unique');
        });
    }

    /**
     * Migrer les données existantes
     */
    private function migrateExistingData(): void
    {
        // Récupérer la première spécialité disponible pour créer des profils par défaut
        $defaultSpecialty = DB::table('specialties')->first();
        
        if (!$defaultSpecialty) {
            // Si aucune spécialité n'existe, on ne peut pas créer de profils
            // Dans ce cas, on garde doctor_profile_id nullable
            return;
        }

        // Pour chaque médecin, associer ses schedules/unavailabilities/delays à son profil
        $doctors = DB::table('users')
            ->where('role', 'doctor')
            ->get();

        foreach ($doctors as $doctor) {
            // Récupérer le profil du médecin
            $profile = DB::table('doctor_profiles')
                ->where('user_id', $doctor->id)
                ->first();

            // Si le médecin n'a pas de profil, créer un profil par défaut
            if (!$profile) {
                // Vérifier si le médecin a déjà un profil avec la spécialité par défaut
                $existingProfile = DB::table('doctor_profiles')
                    ->where('user_id', $doctor->id)
                    ->where('specialty_id', $defaultSpecialty->id)
                    ->first();
                
                if ($existingProfile) {
                    $profile = $existingProfile;
                } else {
                    // Créer un nouveau profil avec la spécialité par défaut
                    try {
                        $profileId = DB::table('doctor_profiles')->insertGetId([
                            'user_id' => $doctor->id,
                            'specialty_id' => $defaultSpecialty->id,
                            'qualification' => 'Médecin généraliste',
                            'max_patients_per_day' => 20,
                            'average_consultation_time' => 30,
                            'is_available' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $profile = (object)['id' => $profileId];
                    } catch (\Exception $e) {
                        // Si l'insertion échoue (violation de contrainte unique), utiliser le profil existant
                        $profile = DB::table('doctor_profiles')
                            ->where('user_id', $doctor->id)
                            ->where('specialty_id', $defaultSpecialty->id)
                            ->first();
                    }
                }
            }

            // Associer les schedules au profil
            DB::table('doctor_schedules')
                ->where('doctor_id', $doctor->id)
                ->whereNull('doctor_profile_id')
                ->update(['doctor_profile_id' => $profile->id]);

            // Associer les unavailabilities au profil
            DB::table('doctor_unavailabilities')
                ->where('doctor_id', $doctor->id)
                ->whereNull('doctor_profile_id')
                ->update(['doctor_profile_id' => $profile->id]);

            // Associer les delays au profil et initialiser is_active
            DB::table('doctor_delays')
                ->where('doctor_id', $doctor->id)
                ->whereNull('doctor_profile_id')
                ->update([
                    'doctor_profile_id' => $profile->id,
                    'is_active' => true
                ]);
        }
    }

    /**
     * Obtenir le nom de l'index unique pour une table et des colonnes données
     */
    private function getUniqueIndexName(string $table, array $columns): ?string
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}`");
            
            // Grouper les index par nom
            $indexGroups = [];
            foreach ($indexes as $index) {
                $keyName = $index->Key_name;
                if ($keyName === 'PRIMARY') {
                    continue;
                }
                
                if (!isset($indexGroups[$keyName])) {
                    $indexGroups[$keyName] = [
                        'name' => $keyName,
                        'non_unique' => $index->Non_unique,
                        'columns' => []
                    ];
                }
                $indexGroups[$keyName]['columns'][] = $index->Column_name;
            }
            
            // Chercher un index unique avec les colonnes correspondantes
            foreach ($indexGroups as $indexGroup) {
                if ($indexGroup['non_unique'] == 0) { // Index unique
                    $indexColumns = $indexGroup['columns'];
                    
                    if (count($indexColumns) === count($columns) && 
                        empty(array_diff($indexColumns, $columns)) &&
                        empty(array_diff($columns, $indexColumns))) {
                        return $indexGroup['name'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Si la table n'existe pas ou autre erreur, retourner null
            return null;
        }
        
        return null;
    }

    /**
     * Vérifier si une colonne existe dans une table
     */
    private function columnExists(string $table, string $column): bool
    {
        try {
            $columns = DB::select(
                "SELECT COUNT(*) as count 
                 FROM information_schema.COLUMNS 
                 WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = ? 
                 AND COLUMN_NAME = ?",
                [$table, $column]
            );
            return !empty($columns) && $columns[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
