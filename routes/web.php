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

Route::get('/qmatic', [App\Http\Controllers\QmaticController::class, 'index'])->name('qmatic.index');

// Routes Qmatic - Affichage public (pas d'authentification requise)
Route::prefix('qmatic/display')->name('qmatic.display.')->group(function () {
    Route::get('/', [App\Http\Controllers\Qmatic\DisplayController::class, 'index'])->name('index');
    Route::get('/fullscreen', [App\Http\Controllers\Qmatic\DisplayController::class, 'fullscreen'])->name('fullscreen');
    Route::get('/updates', [App\Http\Controllers\Qmatic\DisplayController::class, 'updates'])->name('updates');
});

// Routes Qmatic - Borne de prise de ticket (pas d'authentification requise pour les usagers)
Route::prefix('qmatic/kiosk')->name('qmatic.kiosk.')->group(function () {
    Route::get('/', [App\Http\Controllers\Qmatic\KioskController::class, 'index'])->name('index');
    Route::post('/generate', [App\Http\Controllers\Qmatic\KioskController::class, 'generateTicket'])->name('generate');
    Route::get('/ticket/{ticket}', [App\Http\Controllers\Qmatic\KioskController::class, 'showTicket'])->name('ticket');
    Route::get('/ticket/{ticket}/status', [App\Http\Controllers\Qmatic\KioskController::class, 'checkTicketStatus'])->name('ticket.status');
});

// Routes Qmatic - Authentification
Route::prefix('qmatic')->name('qmatic.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Qmatic\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Qmatic\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Qmatic\AuthController::class, 'logout'])->name('logout');
});

// Routes Qmatic authentifiées (Guard: qmatic)
Route::middleware('auth:qmatic')->prefix('qmatic')->name('qmatic.')->group(function () {
    
    // Interface Agent
    Route::prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Qmatic\AgentController::class, 'dashboard'])->name('dashboard');
        Route::post('/counter/assign', [App\Http\Controllers\Qmatic\AgentController::class, 'assignCounter'])->name('counter.assign');
        Route::post('/counter/release', [App\Http\Controllers\Qmatic\AgentController::class, 'releaseCounter'])->name('counter.release');
        Route::post('/call-next', [App\Http\Controllers\Qmatic\AgentController::class, 'callNext'])->name('call-next');
        Route::post('/recall', [App\Http\Controllers\Qmatic\AgentController::class, 'recall'])->name('recall');
        Route::post('/start-serving', [App\Http\Controllers\Qmatic\AgentController::class, 'startServing'])->name('start-serving');
        Route::post('/mark-served', [App\Http\Controllers\Qmatic\AgentController::class, 'markAsServed'])->name('mark-served');
        Route::post('/mark-absent', [App\Http\Controllers\Qmatic\AgentController::class, 'markAsAbsent'])->name('mark-absent');
        Route::post('/requeue', [App\Http\Controllers\Qmatic\AgentController::class, 'requeue'])->name('requeue');
    });
});

// Routes Qmatic Administration (Guard: web - Utilisateurs principaux VitalBridge)
Route::middleware('auth')->prefix('qmatic/admin')->name('qmatic.admin.')->group(function () {
    // Administration des services
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [App\Http\Controllers\Qmatic\ServiceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Qmatic\ServiceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Qmatic\ServiceController::class, 'store'])->name('store');
        Route::get('/{service}/edit', [App\Http\Controllers\Qmatic\ServiceController::class, 'edit'])->name('edit');
        Route::put('/{service}', [App\Http\Controllers\Qmatic\ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [App\Http\Controllers\Qmatic\ServiceController::class, 'destroy'])->name('destroy');
        Route::post('/{service}/toggle', [App\Http\Controllers\Qmatic\ServiceController::class, 'toggleStatus'])->name('toggle');
    });

    // Gestion des Agents
    Route::resource('users', App\Http\Controllers\Qmatic\Admin\UserController::class);

    // Gestion des Guichets
    Route::resource('counters', App\Http\Controllers\Qmatic\Admin\CounterController::class);

    // Paramètres globaux
    Route::get('/settings', [App\Http\Controllers\Qmatic\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Qmatic\Admin\SettingController::class, 'update'])->name('settings.update');
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
