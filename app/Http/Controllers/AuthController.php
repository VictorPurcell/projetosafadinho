<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Duo\DuoUniversal\Client;
use Duo\DuoUniversal\DuoException;
use Illuminate\Http\RedirectResponse;
use App\Services\DuoService;
use App\Services\AuthenticationService;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\SessionService;


class AuthController extends Controller
{
    protected DuoService $duoService;
    protected AuthenticationService $authService;
    protected SessionService $sessionService;

    public function __construct(DuoService $duoService, AuthenticationService $authService, SessionService $sessionService)
    {
        $this->duoService = $duoService;
        $this->authService = $authService;
        $this->sessionService = $sessionService;
    }

    /**
     * Trata o login do primeiro fator e inicia o fluxo 2FA com o Duo.
     */
    public function handleLogin(LoginRequest $request): RedirectResponse
    {
        Log::info("Iniciando processo de login.");
        
        // Obtém as credenciais validadas.
        $credentials = $request->only('username', 'password');

        // Tenta autenticar usando o AuthenticationService.
        try {
            $this->authService->authenticate($credentials, $request->boolean('remember'), $request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Falha na autenticação: " . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors());
        }

        Log::info("Usuário autenticado via Laravel: " . auth()->user()->username);
        $this->sessionService->regenerateSession();

        // Delegação do fluxo 2FA para o DuoLoginController.
        $user = auth()->user();
        return app(DuoLoginController::class)->initiateFlow($user->username);
    }

    /**
     * Processa o callback do Duo, validando o state e o código, e finaliza a autenticação 2FA.
     */
    public function handleDuoCallback(Request $request)
    {
        Log::info("Processando callback do Duo.");
        if ($request->has('error')) {
            $errorMsg = $request->input('error') . ": " . $request->input('error_description');
            return app(DuoErrorController::class)->handleGenericError("Erro no callback do Duo: " . $errorMsg);
        }

        $state = $request->input('state');
        $duoCode = $request->input('duo_code');
        $username = session('username');
        $savedState = session('duo_state');

        if (!$savedState || !$username) {
            return app(DuoErrorController::class)->handleGenericError("Sessão Duo expirada.");
        }
        
        if ($state !== $savedState) {
            session()->forget('duo_state');
            return app(DuoErrorController::class)->handleGenericError("O estado retornado pelo Duo não confere com o salvo.", $username);
        }

        try {
            $decodedToken = $this->duoService->exchangeAuthorizationCode($duoCode, $username);
            Log::info("Token 2FA obtido com sucesso para o usuário: " . $username);
        } catch (DuoException $e) {
            return app(DuoErrorController::class)->handleGenericError("Erro ao trocar o código de autorização: " . $e->getMessage(), $username);
        }

        if (!isset($decodedToken['exp'])) {
            return app(DuoErrorController::class)->handleTokenError("Chave 'exp' não encontrada no token.", $username);
        }

        if (time() > $decodedToken['exp']) {
            return app(DuoErrorController::class)->handleExpiredToken($username);
        }

        session()->put('duo_authenticated', true);
        Log::info("2FA concluído com sucesso para o usuário: " . $username);
        return redirect()->route('dashboard');
    }
    /**
     * Realiza o logout e limpa a sessão.
     */
    public function logout()
{
    app(SessionService::class)->logout();
    return redirect()->route('login')->with('message', 'Logout realizado com sucesso!');
}

}
