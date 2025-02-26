<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SessionService
{
    /**
     * Armazena dados na sessão.
     *
     * @param array $data
     * @return void
     */
    public function store(array $data): void
    {
        foreach ($data as $key => $value) {
            Session::put($key, $value);
            Log::info("Chave '{$key}' armazenada na sessão com valor: " . json_encode($value));
        }
    }

    /**
     * Recupera um valor da sessão.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Session::get($key, $default);
    }

    /**
     * Remove uma chave específica da sessão.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void
    {
        Session::forget($key);
        Log::info("Chave '{$key}' removida da sessão.");
    }

    /**
     * Limpa todos os dados da sessão e desloga o usuário.
     *
     * @return void
     */
    public function clearSession(): void
    {
        Auth::logout();
        Session::flush();
        Log::info("Sessão limpa e usuário deslogado com sucesso.");
    }

    /**
     * Regenera a sessão para prevenir fixação.
     *
     * @return void
     */
    public function regenerateSession(): void
    {
        session()->regenerate();
        Log::info("Sessão regenerada com sucesso.");
    }

    /**
     * Realiza o logout completo: desloga o usuário, invalida a sessão e regenera o token.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        Log::info("Logout realizado com sucesso.");
    }
}
