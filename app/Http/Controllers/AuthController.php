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
        } catch (DuoException $e) {
            throw new \RuntimeException("Erro de configuração do Duo: " . $e->getMessage());
        }

        $this->duoFailmode = strtoupper(config('duo.failmode'));
    }

    /**
     * Exibe a tela de login.
     */
    public function showLogin()
    {
        return view('auth.login', ['message' => 'Por favor, faça login']);
    }

    /**
     * Trata o login do primeiro fator e inicia o fluxo 2FA com o Duo.
     */
    public function handleLogin(Request $request)
    {
        // Validação básica dos campos de login.
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Tenta autenticar o usuário com Laravel.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()->back()->withErrors(['username' => __('auth.failed')]);
        }

        // Limpa qualquer flag de 2FA anterior.
        Session::forget('duo_authenticated');

        // Verifica se o serviço Duo está disponível.
        try {
            $this->duoClient->healthCheck();
        } catch (DuoException $e) {
            Log::error($e->getMessage());
            if ($this->duoFailmode === "OPEN") {
                Session::put('duo_authenticated', true);
                return redirect()->route('login');
            }
            return $this->handleError("2FA indisponível. Verifique a configuração do Duo.");
        }

        // Obtenha o usuário autenticado.
        $user = Auth::user();
        $username = $user->username; // Usa username se existir, caso contrário email.

        // Gera um state único e armazena na sessão.
        $state = $this->duoClient->generateState();
        Session::put('duo_state', $state);
        Session::put('duo_username', $username);

        // Redireciona para a URL do Duo para autenticação do segundo fator.
        return redirect()->away(
            $this->duoClient->createAuthUrl($username, $state)
        );
    }

    /**
     * Processa o callback do Duo, validando o state e o código, e finaliza a autenticação 2FA.
     */
    public function handleDuoCallback(Request $request)
    {
        if ($request->has('error')) {
            $errorMsg = $request->input('error') . ": " . $request->input('error_description');
            Log::error($errorMsg);
            return redirect()->route('auth.login')->withErrors(['duo' => "Erro no Duo: " . $errorMsg]);
        }

        $state     = $request->input('state');
        $duoCode   = $request->input('duo_code');
        $username  = Session::get('duo_username');
        $savedState = Session::get('duo_state');

        if (empty($savedState) || empty($username)) {
            return redirect()->route('login')->withErrors(['message' => 'Sessão Duo expirada. Faça login novamente.']);
        }

        if ($state !== $savedState) {
            return redirect()->route('login')->withErrors(['duo' => 'O estado retornado pelo Duo não confere com o salvo.']);
        }

        try {
            $decodedToken = $this->duoClient->exchangeAuthorizationCodeFor2FAResult($duoCode, $username);
        } catch (DuoException $e) {
            Log::error($e->getMessage());
            return redirect()->route('auth.login')->withErrors(['duo' => 'Erro ao decodificar a resposta do Duo. Verifique o relógio do dispositivo.']);
        }

        Session::put('duo_authenticated', true);

        return redirect()->route('dashboard');
    }
    /**
    * Limpa a sessão e redireciona para a página de login com uma mensagem de erro.
    */
    private function handleError(string $errorMessage, string $route = 'login'): RedirectResponse
{
    Auth::logout(); // Encerra a sessão do usuário
    Session::flush(); // Limpa todos os dados da sessão
    Log::error($errorMessage); // Registra o erro no log

    return redirect()->route($route)->withErrors(['duo' => $errorMessage]);
}

    /**
     * Realiza o logout e limpa a sessão.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->with('message', 'Logout realizado com sucesso!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
