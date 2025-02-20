<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureDuoAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('duo_authenticated') || ! session('duo_authenticated')) {
            return redirect()->route('duo.auth');
        }
        return $next($request);
    }
}
