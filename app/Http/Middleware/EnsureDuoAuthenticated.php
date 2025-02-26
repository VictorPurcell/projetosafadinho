<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EnsureDuoAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('duo_authenticated', false)) {
            return redirect()->route('login')->withErrors(['duo' => 'Autenticação 2FA obrigatória.']);
        }

        return $next($request);
    }
}
