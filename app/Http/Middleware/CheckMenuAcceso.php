<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuAcceso
{
    public function handle(Request $request, Closure $next)
    {
        $ruta = $request->route()->getName();
        // Rutas que no requieren validación de menú
        if (in_array($ruta, ['dashboard', 'login'])) {
            return $next($request);
        }

        $user = Auth::user();
        if ($user && !$user->tieneAcceso($ruta)) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        return $next($request);
    }
}