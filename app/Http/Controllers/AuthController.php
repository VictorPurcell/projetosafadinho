<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Duo\DuoUniversal\Client;
use Duo\DuoUniversal\DuoException;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    protected Client $duoClient;
    protected string $duoFailmode;

    public function __construct()
    {
        try {
            $this->duoClient = new Client(
                config('duo.client_id'),
                config('duo.client_secret'),
                config('duo.api_hostname'),
                config('duo.redirect_uri'),
                true,
                config('duo.http_proxy') ?? null
            );
            Log::info("Cliente Duo configurado com sucesso.");
        } catch (DuoException $e) {
            Log::error("Erro de configuração do Duo: " . $e->getMessage());
            throw new \RuntimeException("Erro de configuração do Duo: " . $e->getMessage(), $e->getCode(), $e);
        }

        $this->duoFailmode = strtoupper(config('duo.failmode', 'CLOSED'));
        Log::info("Failmode configurado: " . $this->duoFailmode);
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
         * Trata o login do primeiro fator e inicia o fluxo 2FA com o Duo.
         */
        public function handleLogin(Request $request)
        {
        Log::info("Iniciando processo de login.");
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string']
        ]);      

        // Tenta autenticar o usuário com Laravel.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            Log::error("Falha na autenticação do usuário: " . $request->input('username'));
            return redirect()->back()->withErrors(['username' => __('auth.failed')]);
        }
        Log::info("Usuário autenticado via Laravel: " . Auth::user()->username);

        $request->session()->regenerate();

        Session::forget('duo_authenticated');

        try {
            $this->duoClient->healthCheck();
            Log::info("HealthCheck do Duo realizado com sucesso.");
        } catch (DuoException $e) {
            Log::error("HealthCheck Duo falhou: " . $e->getMessage());
        
            // Se o failmode for "CLOSED", permitir o login sem 2FA
            if ($this->duoFailmode === "CLOSED") {
                Log::warning("Duo indisponível e failmode está em CLOSED. Permitindo login sem 2FA.");
                Session::put('duo_authenticated', true);
                return redirect()->route('dashboard');
            }
            
            // Se o failmode for "OPEN", seguir com o fluxo normal e mostrar erro
            return $this->handleError("2FA indisponível e o sistema está configurado para fail OPEN. Não é possível fazer login.");
        }

        // Se o failmode for OPEN, permite o login sem 2FA.
        // Session::put('duo_authenticated', true);
        // return redirect()->route('dashboard');
        
        // Obtenha o usuário autenticado.
        $user = Auth::user();
        $username = $user->username; // Usa username se existir, caso contrário, ajuste para email se necessário.

        $state = $this->duoClient->generateState();
        Session::put('duo_state', $state);
        Session::put('username', $username);
        Log::info("State gerado para o Duo e armazenado na sessão para o usuário: " . $username);

        $authUrl = $this->duoClient->createAuthUrl($username, $state);
        Log::info("Redirecionando usuário para o Duo para autenticação 2FA: " . $authUrl);
        return redirect()->away($authUrl);
        }

        /**
         * Processa o callback do Duo, validando o state e o código, e finaliza a autenticação 2FA.
         */
        public function handleDuoCallback(Request $request)
        {
        Log::info("Processando callback do Duo.");
        if ($request->has('error')) {
            $errorMsg = $request->input('error') . ": " . $request->input('error_description');
            Log::error("Erro no callback do Duo: " . $errorMsg);
            return redirect()->route('auth.login')->withErrors(['duo' => "Erro no Duo: " . $errorMsg]);
        }

        $state     = $request->input('state');
        $duoCode   = $request->input('duo_code');
        $username  = Session::get('username');
        $savedState = Session::get('duo_state');

        if (!Session::has('duo_state') || !Session::has('username')) {
            Log::error("Sessão Duo expirada.");
            return redirect()->route('login')->withErrors(['duo' => 'Sessão Duo expirada. Faça login novamente.']);
        }
        
        if ($state !== $savedState) {
            Session::forget('duo_state'); // Evita reutilização
            Log::error("Estado retornado pelo Duo não confere para o usuário: " . $username);
            return redirect()->route('login')->withErrors(['duo' => 'O estado retornado pelo Duo não confere com o salvo.']);
        }

        try {
            $decodedToken = $this->duoClient->exchangeAuthorizationCodeFor2FAResult($duoCode, $username);
            Log::info("Token 2FA obtido com sucesso para o usuário: " . $username);
        } catch (DuoException $e) {
            Log::error("Erro ao trocar o código de autorização para o usuário " . $username . ": " . $e->getMessage());
            return redirect()->route('login')->withErrors(['duo' => 'Erro ao decodificar a resposta do Duo. Verifique o relógio do dispositivo.']);
        }

        if (!isset($decodedToken['exp'])) {
            Log::error("Token Duo inválido: chave 'exp' não encontrada para o usuário: " . $username);
            return redirect()->route('login')->withErrors(['duo' => 'Token Duo inválido.']);
        }

        if (time() > $decodedToken['exp']) {
            Log::error("Token Duo expirado para o usuário: " . $username);
            return redirect()->route('login')->withErrors(['duo' => 'Token Duo expirado. Por favor, tente novamente.']);
        }

        Session::put('duo_authenticated', true);
        Log::info("2FA concluído com sucesso para o usuário: " . $username);

        return redirect()->route('dashboard');
        }

        /**
         * Limpa a sessão e redireciona para a página de login com uma mensagem de erro.
         */
        private function handleError(string $errorMessage, string $route = 'login'): RedirectResponse
        {
        Auth::logout(); // Encerra a sessão do usuário
        Session::flush(); // Limpa todos os dados da sessão
        Log::error("Erro durante autenticação: " . $errorMessage);
        return redirect()->route($route)->withErrors(['duo' => $errorMessage]);
        }
        /**
         * Realiza o logout e limpa a sessão.
         */
        public function logout(Request $request)
        {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info("Logout realizado com sucesso.");
        return redirect()->route('login')->with('message', 'Logout realizado com sucesso!');
        }
}
