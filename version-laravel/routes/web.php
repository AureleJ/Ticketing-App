<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
// use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store']);

    Route::get('/signup', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/signup', [RegisteredUserController::class, 'store']);

    // Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    // Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
    Route::put('/tickets/{id}/close', [TicketController::class, 'close'])->name('tickets.close');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('account.profile');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('account.settings');
});