<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;

// Ticket
Route::post('/tickets', [TicketController::class, 'store'])->name('api.tickets.store');
Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('api.tickets.update');

// Project
Route::post('/projects', [ProjectController::class, 'store'])->name('api.projects.store');

// Client
Route::post('/clients', [ClientController::class, 'store'])->name('api.clients.store');