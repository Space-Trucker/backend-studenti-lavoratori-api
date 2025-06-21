<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController; // Assuming you'll create this controller
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Assuming you'll create this controller

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Registration route
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest'); // 'guest' middleware ensures only unauthenticated users can register

// Login route
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// Logout route
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum'); // 'auth:sanctum' middleware ensures only authenticated users can logout
// Rotte API per la gestione dei Programmi (Schedules)
// Questo creerà automaticamente le rotte GET, POST, PUT/PATCH, DELETE per i programmi.
// Ad esempio: GET /api/schedules, POST /api/schedules, PUT /api/schedules/{id}, DELETE /api/schedules/{id}
// Queste rotte sono protette automaticamente dal middleware 'auth:sanctum'
// che abbiamo messo nel costruttore di ScheduleController.
Route::apiResource('schedules', ScheduleController::class);

// Rotte API per la gestione delle Date Importanti (Important Dates)
// Anche questo creerà le rotte CRUD complete per le date importanti.
// Ad esempio: GET /api/important-dates, POST /api/important-dates, etc.
// Protette dal middleware 'auth:sanctum' in ImportantDateController.
Route::apiResource('important-dates', ImportantDateController::class);
