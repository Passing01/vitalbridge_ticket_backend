<?php

use App\Http\Controllers\Api\Auth\AuthController;
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
