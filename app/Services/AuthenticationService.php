<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthenticationService
{
    /**
     * Tenta autenticar o usuário com as credenciais fornecidas.
     * Se a autenticação falhar, incrementa o rate limiter e lança uma exceção.
     *
     * @param array   $credentials
     * @param bool    $remember
     * @param Request $request
     * @return bool
     *
     * @throws ValidationException
     */
    public function authenticate(array $credentials, bool $remember, Request $request): bool
    {
        $throttleKey = $this->throttleKey($request);

        // Verifica se o limite de tentativas foi alcançado.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // Tenta autenticar
        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        // Autenticação bem-sucedida; limpa o rate limiter.
        RateLimiter::clear($throttleKey);
        return true;
    }

    /**
     * Gera a chave de throttle para limitar as tentativas de login.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());
    }
}
