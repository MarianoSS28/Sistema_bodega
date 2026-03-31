<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTerminos
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('aceptar-terminos', 'login', 'logout', 'mantenimiento', 'terminos.publico')) {
            return $next($request);
        }

        if (auth()->check() && (int)(auth()->user()->acepto_terminos ?? 0) === 0) {
            return redirect()->route('aceptar-terminos');
        }

        return $next($request);
    }
}