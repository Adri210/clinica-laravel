<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
  public function handle(Request $request, Closure $next, ...$roles): Response
  {
      if (!auth()->check()) {
          return redirect()->route('login');
      }

      $userRole = auth()->user()->tipo_usuario;
      
      if (!in_array($userRole, $roles)) {
          return redirect()->route('dashboard')
              ->with('error', 'Você não tem permissão para acessar esta área.');
      }

      return $next($request);
  }
}