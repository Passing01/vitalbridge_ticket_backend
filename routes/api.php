<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DoctorProfileController;
use App\Http\Controllers\Api\HealthCenterController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\DoctorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1/auth')->group(function () {
    // Register new patient (sends OTP automatically)
    Route::post('/register', [AuthController::class, 'register']);
    
    // Request OTP for login
    Route::post('/request-otp', [AuthController::class, 'sendOTP']);
    
    // Verify OTP (used after registration or login) - requires OTP verification token
    Route::middleware(['auth:sanctum', 'otp.verify'])->group(function () {
        Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    });
    
    // Protected routes (require full authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Get authenticated user
        Route::get('/me', [AuthController::class, 'me']);
        
        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Routes pour la gestion du profil médecin (accès protégé)
Route::prefix('v1/doctor')->middleware(['auth:sanctum', 'role:doctor'])->group(function () {
    // Récupérer le profil complet du médecin
    Route::get('/profile', [DoctorProfileController::class, 'getProfile']);
    
    // Gestion de la file d'attente
    Route::prefix('{doctor}/queue')->group(function () {
        // Voir la file d'attente
        Route::get('/', [\App\Http\Controllers\Api\QueueController::class, 'index']);
        
        // Appeler le prochain patient
        Route::post('/next', [\App\Http\Controllers\Api\QueueController::class, 'callNext']);
    });
    
    // Actions sur un rendez-vous spécifique
    Route::prefix('appointments/{appointment}')->group(function () {
        // Marquer comme servi
        Route::post('/serve', [\App\Http\Controllers\Api\QueueController::class, 'markAsServed']);
        
        // Marquer comme absent
        Route::post('/absent', [\App\Http\Controllers\Api\QueueController::class, 'markAsAbsent']);
        
        // Réinsérer dans la file d'attente
        Route::post('/requeue', [\App\Http\Controllers\Api\QueueController::class, 'requeue']);
        
        // Marquer comme urgent
        Route::post('/urgent', [\App\Http\Controllers\Api\QueueController::class, 'markAsUrgent']);
    });
    
    // Gestion des retards
    Route::post('/delay', [DoctorProfileController::class, 'setDelay']);
    
    // Gestion des indisponibilités
    Route::post('/unavailability', [DoctorProfileController::class, 'addUnavailability']);
    Route::delete('/unavailability/{id}', [DoctorProfileController::class, 'removeUnavailability']);
});

// Routes publiques pour la consultation des médecins et centres de santé
Route::prefix('v1')->group(function () {
    // Centres de santé (reception)
    Route::get('/health-centers', [HealthCenterController::class, 'index']);
    Route::get('/health-centers/{id}', [HealthCenterController::class, 'show']);
    
    // Départements
    Route::get('/departments/{id}', [DepartmentController::class, 'show'])
        ->where('id', '.*'); // Accepter n'importe quel caractère dans l'ID
    
    // Spécialités
    Route::get('/specialties/{id}', [SpecialtyController::class, 'show'])
        ->where('id', '.*'); // Accepter n'importe quel caractère dans l'ID
    
    // Médecins
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);
    
    // Rendez-vous (patient)
    Route::middleware('auth:sanctum')->group(function () {
        // Récupérer les créneaux disponibles pour un médecin
        Route::get('/doctors/{doctor}/available-slots', [\App\Http\Controllers\Api\AppointmentController::class, 'getAvailableSlots']);
        
        // Gestion des rendez-vous (patient)
        Route::post('/appointments', [\App\Http\Controllers\Api\AppointmentController::class, 'store']);
        Route::get('/patient/appointments', [\App\Http\Controllers\Api\AppointmentController::class, 'patientAppointments']);
        Route::get('/patient/appointments/{id}', [\App\Http\Controllers\Api\AppointmentController::class, 'showPatientAppointment']);
        Route::post('/appointments/{id}/cancel', [\App\Http\Controllers\Api\AppointmentController::class, 'cancel']);
        
        // Gestion des rendez-vous (médecin)
        Route::middleware('role:doctor')->group(function () {
            Route::get('/doctor/appointments', [\App\Http\Controllers\Api\AppointmentController::class, 'doctorAppointments']);
            Route::get('/doctor/appointments/{id}', [\App\Http\Controllers\Api\AppointmentController::class, 'showDoctorAppointment']);
            Route::post('/appointments/{id}/complete', [\App\Http\Controllers\Api\AppointmentController::class, 'complete']);
        });
    });
});

