<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckMantenimiento
{
    public function handle(Request $request, Closure $next)
    {
        // Rutas siempre accesibles
        if ($request->routeIs('login', 'logout', 'mantenimiento')) {
            return $next($request);
        }

        try {
            $result = DB::select('EXEC bodega.sp_get_parametro @nombre=?', ['MODO_MANTENIMIENTO']);
            $activo = !empty($result) && $result[0]->valor === '1';
        } catch (\Throwable) {
            // Si la BD no responde, no bloquear
            return $next($request);
        }

        if ($activo) {
            // Los admins (id_rol = 1) pueden seguir usando el sistema
            if (auth()->check() && (int) auth()->user()->id_rol === 1) {
                return $next($request);
            }
            return redirect()->route('mantenimiento');
        }

        return $next($request);
    }
}