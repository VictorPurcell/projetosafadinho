<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class DuoErrorController extends Controller
{
    /**
     * Trata o erro quando o token não possui a chave 'exp'.
     *
     * @param string $errorMessage
     * @param string|null $username
     * @return RedirectResponse
     */
    public function handleTokenError(string $errorMessage, ?string $username = null): RedirectResponse
    {
        Log::error("Token Duo inválido: " . $errorMessage . " para o usuário: " . ($username ?? 'desconhecido'));
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->withErrors(['duo' => $errorMessage]);
    }
    
    /**
     * Trata o caso de token expirado.
     *
     * @param string $username
     * @return RedirectResponse
     */
    public function handleExpiredToken(string $username): RedirectResponse
    {
        Log::error("Token Duo expirado para o usuário: " . $username);
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->withErrors(['duo' => 'Token Duo expirado. Por favor, tente novamente.']);
    }
    
    /**
     * Trata um erro genérico relacionado ao Duo.
     *
     * @param string $errorMessage
     * @param string|null $username
     * @return RedirectResponse
     */
    public function handleGenericError(string $errorMessage, ?string $username = null): RedirectResponse
    {
        Log::error("Erro genérico do Duo: " . $errorMessage . " para o usuário: " . ($username ?? 'desconhecido'));
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->withErrors(['duo' => $errorMessage]);
    }
}
