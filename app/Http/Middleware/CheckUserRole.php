<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role)
{
    if (!$request->user() || $request->user()->tipo_usuario !== $role) {
        return response()->json(['message' => 'Acesso não autorizado.'], 403);
    }
    return $next($request);
}
}
