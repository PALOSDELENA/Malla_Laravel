<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PuntoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $punto): Response
    {
        $user = Auth::user();

        if(!$user){
            return redirect()->route('loginInitial');
        }

        if (!$user->punto || !in_array($user->punto->nombre, (array) $punto)) {
            abort(403, "No tienes permiso para acceder a esta página.");
        }
        return $next($request);
    }
}
