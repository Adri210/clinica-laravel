<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MedicoController;


Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [UsuarioController::class, 'login']);



Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function () {
    Auth::logout(); // Faz o logout do usuário
    return redirect('/login'); // Redireciona para a página de login
})->name('logout');


// Usuários
Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');


// Agenda
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

// Médicos
Route::get('/medicos', [MedicoController::class, 'index'])->name('medicos.index');
Route::get('/medicos/create', [MedicoController::class, 'create'])->name('medicos.create');
Route::post('/medicos', [UsuarioController::class, 'store'])->name('medicos.store');
