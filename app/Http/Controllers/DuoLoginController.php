<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Services\DuoService;
use Duo\DuoUniversal\DuoException;

class DuoLoginController extends Controller
{
    protected DuoService $duoService;

    public function __construct(DuoService $duoService)
    {
        $this->duoService = $duoService;
    }


    /**
     * Exibe a tela de login.
     */
    public function showLogin()
    {
        Log::info("Exibindo tela de login.");
        return view('auth.login', ['message' => 'Por favor, faça login']);
    }
    /**
     * Inicia o fluxo do Duo e redireciona para a página de autenticação 2FA.
     *
     * @param string $username
     * @return RedirectResponse
     */
    public function initiateFlow(string $username): RedirectResponse
    {
        // Limpa dados anteriores do Duo.
        Session::forget('duo_authenticated');

        try {
            $this->duoService->healthCheck();
            Log::info("DuoLoginController: HealthCheck do Duo realizado com sucesso.");
        } catch (DuoException $e) {
            Log::error("DuoLoginController: HealthCheck Duo falhou: " . $e->getMessage());
            // Se o failmode for "CLOSED", permite o login sem 2FA.
            if ($this->duoService->getFailmode() === "CLOSED") {
                Log::warning("Duo indisponível e failmode está em CLOSED. Permitindo login sem 2FA.");
                Session::put('duo_authenticated', true);
                return redirect()->route('dashboard');
            }
            // Se o failmode for "OPEN", bloqueia o login.
            return redirect()->route('login')->withErrors(['duo' => '2FA indisponível e o sistema está configurado para fail OPEN. Não é possível fazer login.']);
        }

        // Se o healthCheck passou, continua o fluxo de 2FA.
        $state = $this->duoService->generateState();
        Session::put('duo_state', $state);
        Session::put('username', $username);
        Log::info("DuoLoginController: State gerado para o Duo e armazenado na sessão para o usuário: " . $username);

        $authUrl = $this->duoService->createAuthUrl($username, $state);
        Log::info("DuoLoginController: Redirecionando usuário para o Duo para autenticação 2FA: " . $authUrl);
        return redirect()->away($authUrl);
    }
}
