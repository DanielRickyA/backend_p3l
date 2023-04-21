<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotInstruktur
{
    public function handle($request, Closure $next, $guard = "Instruktur")
    {
        if (!auth()->guard($guard)->check()) {
            return redirect(route('loginInstruktur'));
        }
        return $next($request);
    }
}
