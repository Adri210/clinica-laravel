<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\MedicoController;

// Rotas públicas (login)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [UsuarioController::class, 'login']);

// Dashboard (apenas admin)
Route::get('/dashboard', function () {
    if (auth()->user()->tipo_usuario !== 'admin') {
        return redirect()->route('login')->with('error', 'Acesso não autorizado');
    }
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Usuários (apenas admin)
Route::get('/usuarios', function () {
    if (auth()->user()->tipo_usuario !== 'admin') {
        return redirect()->route('dashboard')->with('error', 'Acesso não autorizado');
    }
    return app(UsuarioController::class)->index();
})->middleware('auth')->name('usuarios.index');

Route::get('/usuarios/create', function () {
    return app(UsuarioController::class)->create();
})->name('usuarios.create');

Route::post('/usuarios', function (Illuminate\Http\Request $request) {
    return app(UsuarioController::class)->store($request);
})->name('usuarios.store');

// Agenda (apenas admin)
Route::get('/agenda', function () {
    if (auth()->user()->tipo_usuario !== 'admin') {
        return redirect()->route('dashboard')->with('error', 'Acesso não autorizado');
    }
    return app(AgendaController::class)->index();
})->middleware('auth')->name('agenda.index');

// Médicos (apenas admin)
Route::get('/medicos', function () {
    if (auth()->user()->tipo_usuario !== 'admin') {
        return redirect()->route('dashboard')->with('error', 'Acesso não autorizado');
    }
    return app(MedicoController::class)->index();
})->middleware('auth')->name('medicos.index');

Route::get('/medicos/create', function () {
    if (auth()->user()->tipo_usuario !== 'admin') {
        return redirect()->route('dashboard')->with('error', 'Acesso não autorizado');
    }
    return app(MedicoController::class)->create();
})->middleware('auth')->name('medicos.create');

Route::post('/medicos', function (Illuminate\Http\Request $request) {
    if (auth()->user()->tipo_usuario !== 'admin') {
        return redirect()->route('dashboard')->with('error', 'Acesso não autorizado');
    }
    return app(UsuarioController::class)->store($request);
})->middleware('auth')->name('medicos.store');
