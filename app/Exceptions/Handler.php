<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // Trata erros 404 (página não encontrada)
        $this->renderable(function (NotFoundHttpException $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Página não encontrada'], 404);
            }
            
            if (Auth::check()) {
                return response()->view('errors.404', [], 404);
            } else {
                return redirect()->route('login');
            }
        });

        // Trata erros de método não permitido (como o seu caso)
        $this->renderable(function (MethodNotAllowedHttpException $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Método não permitido'], 405);
            }
            
            if (Auth::check()) {
                return response()->view('errors.404', [], 404);
            } else {
                return redirect()->route('login');
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}