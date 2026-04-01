<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCambiarPassword
{
    public function handle(Request $request, Closure $next)
    {
        // Rutas que siempre se permiten
        if ($request->routeIs(
            'login', 'logout', 'mantenimiento',
            'terminos.publico', 'aceptar-terminos', 'cambiar-password'
        )) {
            return $next($request);
        }

        // Permitir Livewire para que las acciones de esas páginas funcionen
        if ($request->hasHeader('X-Livewire')) {
            return $next($request);
        }

        if (!auth()->check()) {
            return $next($request);
        }

        if ((int)(auth()->user()->debe_cambiar_password ?? 0) === 1) {
            return redirect()->route('cambiar-password');
        }

        return $next($request);
    }
}