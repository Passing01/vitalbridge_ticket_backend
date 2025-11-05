<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SpecialtyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
    
});
