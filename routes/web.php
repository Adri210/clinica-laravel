<?php

    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\UsuarioController;
    use App\Http\Controllers\AgendaController;
    use App\Http\Controllers\MedicoController;

    // Autenticação
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');  

    Route::post('/login', [UsuarioController::class, 'login']);
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('auth');

    // Usuários (apenas para admin)
    Route::middleware(['auth', 'check.admin'])->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
    });

    // Médicos (separado completamente)
    Route::resource('medicos', MedicoController::class)->except(['show']);

    // Agenda
 Route::middleware(['auth'])->group(function() {
    Route::controller(AgendaController::class)->prefix('agenda')->name('agenda.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/events', 'getEvents')->name('events');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });
});