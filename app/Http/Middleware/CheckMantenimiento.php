<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckMantenimiento
{
    public function handle(Request $request, Closure $next)
    {
        // Rutas siempre libres sin importar nada
        if ($request->routeIs('login', 'logout', 'mantenimiento', 'terminos.publico', 'aceptar-terminos')) {
            return $next($request);
        }

        // Solo verificar mantenimiento si hay sesión activa
        if (!auth()->check()) {
            return $next($request);
        }

        $user    = auth()->user();
        $esAdmin = (int) $user->id_rol === 1;

        // ── Modo mantenimiento global ──
        try {
            $result = DB::select('EXEC bodega.sp_get_parametro @nombre=?', ['MODO_MANTENIMIENTO']);
            $activo = !empty($result) && $result[0]->valor === '1';
        } catch (\Throwable) {
            $activo = false;
        }

        if ($activo && !$esAdmin) {
            return redirect()->route('mantenimiento');
        }

        // ── Bloqueo de usuario individual ──
        if (!$esAdmin && (int)($user->bloqueado ?? 0) === 1) {
            $motivo = $user->motivo_bloqueo ?? 'Tu cuenta ha sido bloqueada.';
            return redirect()->route('mantenimiento')
                ->with('mensaje_bloqueo', $motivo);
        }

        // ── Bloqueo de comercio ──
        if (!$esAdmin) {
            $comercio = DB::selectOne(
                'SELECT bloqueado, motivo_bloqueo FROM bodega.comercio WHERE id = ? AND estado = 1',
                [$user->id_comercio]
            );
            if ($comercio && (int)$comercio->bloqueado === 1) {
                return redirect()->route('mantenimiento')
                    ->with('mensaje_bloqueo', $comercio->motivo_bloqueo ?? 'Tu comercio ha sido bloqueado.');
            }
        }

        return $next($request);
    }
}