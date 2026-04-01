<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckMantenimiento
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('login', 'logout', 'mantenimiento', 'terminos.publico', 'aceptar-terminos')) {
            return $next($request);
        }

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
            // Si es Livewire, devolver respuesta que fuerce redirect en el cliente
            if ($request->hasHeader('X-Livewire')) {
                return response()->json(['effects' => ['redirect' => route('mantenimiento')]], 200);
            }
            return redirect()->route('mantenimiento');
        }

        // ── Bloqueo de usuario individual ──
        if (!$esAdmin && (int)($user->bloqueado ?? 0) === 1) {
            $motivo  = $user->motivo_bloqueo ?? 'Tu cuenta ha sido bloqueada.';
            $destino = route('mantenimiento', ['tipo' => 'usuario', 'msg' => $motivo]);
            if ($request->hasHeader('X-Livewire')) {
                return response()->json(['effects' => ['redirect' => $destino]], 200);
            }
            return redirect($destino);
        }

        // ── Bloqueo de comercio ──
        if (!$esAdmin) {
            $comercio = DB::selectOne(
                'SELECT bloqueado, motivo_bloqueo FROM bodega.comercio WHERE id = ? AND estado = 1',
                [$user->id_comercio]
            );
            if ($comercio && (int)$comercio->bloqueado === 1) {
                $motivo  = $comercio->motivo_bloqueo ?? 'Tu comercio ha sido bloqueado.';
                $destino = route('mantenimiento', ['tipo' => 'comercio', 'msg' => $motivo]);
                if ($request->hasHeader('X-Livewire')) {
                    return response()->json(['effects' => ['redirect' => $destino]], 200);
                }
                return redirect($destino);
            }
        }

        return $next($request);
    }
}
