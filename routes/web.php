<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Mes patients (réception / admin)
    Route::get('patients', [PatientController::class, 'index'])
        ->name('patients.index')
        ->middleware('role:reception,admin');
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Gestion des files d'attente
    Route::prefix('queues')->name('queues.')->group(function() {
        // Vue générale des files d'attente (pour les administrateurs et réceptionnistes)
        Route::get('/', [\App\Http\Controllers\QueueController::class, 'index'])->name('index');
        
        // Vue d'une file d'attente spécifique (pour les médecins et réceptionnistes)
        Route::get('/doctor/{doctor}', [\App\Http\Controllers\QueueController::class, 'show'])
            ->name('show')
            ->where(['doctor' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
            
        // Vue d'une file d'attente spécifique avec date
        Route::get('/doctor/{doctor}/{date?}', [\App\Http\Controllers\QueueController::class, 'show'])
            ->name('show.date')
            ->where(['doctor' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}', 
                    'date' => '\d{4}-\d{2}-\d{2}']);
        
                // Actions sur la file d'attente
        Route::prefix('{appointment}')->group(function() {
            Route::post('/call', [\App\Http\Controllers\QueueController::class, 'call'])->name('call');
            Route::post('/serve', [\App\Http\Controllers\QueueController::class, 'serve'])->name('serve');
            Route::post('/absent', [\App\Http\Controllers\QueueController::class, 'markAsAbsent'])->name('absent');
            Route::post('/requeue', [\App\Http\Controllers\QueueController::class, 'requeue'])->name('requeue');
            Route::post('/urgent', [\App\Http\Controllers\QueueController::class, 'markAsUrgent'])->name('urgent');
        });
        
        // Routes pour la gestion des rendez-vous
        Route::prefix('appointments')->name('appointments.')->group(function() {
            // Créer un nouveau rendez-vous
            Route::get('/create', [\App\Http\Controllers\AppointmentController::class, 'create'])
                ->name('create');
            // Enregistrer un nouveau rendez-vous
            Route::post('/', [\App\Http\Controllers\AppointmentController::class, 'store'])
                ->name('store');
            // Démarrer un rendez-vous
            Route::post('/{appointment}/start', [\App\Http\Controllers\AppointmentController::class, 'start'])
                ->name('start')
                ->where(['appointment' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
                
            // Marquer un patient comme absent
            Route::post('/{appointment}/absent', [\App\Http\Controllers\AppointmentController::class, 'markAsAbsent'])
                ->name('absent')
                ->where(['appointment' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
                
            // Marquer un patient comme présent
            Route::post('/{appointment}/present', [\App\Http\Controllers\AppointmentController::class, 'markAsPresent'])
                ->name('present')
                ->where(['appointment' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
                
            // Terminer un rendez-vous
            Route::post('/{appointment}/end', [\App\Http\Controllers\AppointmentController::class, 'end'])
                ->name('end')
                ->where(['appointment' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);
        });
    });
});

require __DIR__.'/auth.php';

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Routes pour les médecins
    Route::resource('doctors', DoctorController::class);
    Route::get('doctors/{doctor}/schedule', [DoctorController::class, 'showScheduleForm'])->name('doctors.schedule');
    Route::put('doctors/{doctor}/schedule', [DoctorController::class, 'updateSchedule'])->name('doctors.schedule.update');
    Route::post('doctors/{doctor}/unavailable', [DoctorController::class, 'markUnavailable'])->name('doctors.unavailable');
    Route::post('doctors/{doctor}/delay', [DoctorController::class, 'logDelay'])->name('doctors.delay');
    Route::post('doctors/{doctor}/toggle-status', [DoctorController::class, 'toggleStatus'])->name('doctors.toggle-status');

    // Routes pour les départements
    Route::resource('departments', DepartmentController::class)->except(['show']);
    
    // Route pour récupérer les spécialités d'un département (AJAX)
    Route::get('departments/{department}/specialties', [DepartmentController::class, 'getSpecialties'])
        ->name('departments.specialties');
    
    // Routes pour les spécialités
    Route::get('specialties', [SpecialtyController::class, 'index'])->name('specialties.index');
    Route::get('specialties/create', [SpecialtyController::class, 'create'])->name('specialties.create');
    Route::post('specialties', [SpecialtyController::class, 'store'])->name('specialties.store');
    Route::get('specialties/{specialty}/edit', [SpecialtyController::class, 'edit'])->name('specialties.edit');
    Route::put('specialties/{specialty}', [SpecialtyController::class, 'update'])->name('specialties.update');
    Route::delete('specialties/{specialty}', [SpecialtyController::class, 'destroy'])->name('specialties.destroy');
    
    // Administration des centres de santé (rôle admin uniquement)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('health-centers', [\App\Http\Controllers\Admin\HealthCenterController::class, 'index'])->name('health-centers.index');
        Route::get('health-centers/{id}', [\App\Http\Controllers\Admin\HealthCenterController::class, 'show'])->name('health-centers.show');
        Route::post('health-centers/{id}/toggle-active', [\App\Http\Controllers\Admin\HealthCenterController::class, 'toggleActive'])->name('health-centers.toggle-active');
        Route::post('health-centers/{id}/update-password', [\App\Http\Controllers\Admin\HealthCenterController::class, 'updatePassword'])->name('health-centers.update-password');
    });

    // Page de paiement simulé après inscription
    Route::get('billing/simulate', function () {
        return view('billing.simulate');
    })->name('billing.simulate');
    Route::post('billing/simulate', function () {
        // Ici on simule un paiement sans appel API puis on redirige vers le tableau de bord
        return redirect()->route('dashboard')->with('status', 'Paiement simulé avec succès. Votre compte est maintenant actif.');
    })->name('billing.simulate.submit');
    
});
