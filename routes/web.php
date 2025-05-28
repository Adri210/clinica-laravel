<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MedicoController;
use App\Models\Medico;

// Rotas públicas (login)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [UsuarioController::class, 'login']);

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Usuários
Route::resource('usuarios', UsuarioController::class)->except(['show']);

// Agenda
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::get('/agenda/events', [AgendaController::class, 'getEvents'])->name('agenda.events');
Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
Route::put('/agenda/{event}', [AgendaController::class, 'update'])->name('agenda.update');
Route::delete('/agenda/{event}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

// Médicos - Com Route Model Binding
Route::get('/medicos', [MedicoController::class, 'index'])->name('medicos.index');
Route::get('/medicos/create', [MedicoController::class, 'create'])->name('medicos.create');
Route::post('/medicos', [MedicoController::class, 'store'])->name('medicos.store');
Route::get('/medicos/{medico}/edit', [MedicoController::class, 'edit'])->name('medicos.edit');
Route::put('/medicos/{medico}', [MedicoController::class, 'update'])->name('medicos.update');
Route::delete('/medicos/{medico}', [MedicoController::class, 'destroy'])->name('medicos.destroy');