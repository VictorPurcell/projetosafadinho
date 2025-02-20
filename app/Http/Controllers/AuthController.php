<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Duo\DuoUniversal\Client;
use Duo\DuoUniversal\DuoException;

class AuthController extends Controller
{
    protected $duoClient;
    protected $duoFailmode;

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

    // Exibe a view de login (você pode usar a view Breeze ou customizada)
    public function showLogin()
    {
        return view('login', ['message' => 'Por favor, faça login']);
    }

    // Método de login que intercepta o fluxo Breeze para redirecionar ao Duo
    public function login(Request $request)
    {
        // Validação das credenciais (primeiro fator)
        $credentials = $request->validate([ 
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Tenta autenticar com Laravel
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ]);
        }

        // Limpa qualquer flag de 2FA de sessões anteriores
        Session::forget('duo_authenticated');

        // Verifica se o serviço Duo está disponível
        try {
            $this->duoClient->healthCheck();
        } catch (DuoException $e) {
            Log::error($e->getMessage());
            if ($this->duoFailmode == "OPEN") {
                // Se o modo for OPEN, permite acesso sem 2FA (não recomendado para produção)
                Session::put('duo_authenticated', true);
                return redirect()->route('dashboard');
            } else {
                return back()->withErrors(['duo' => "2FA indisponível. Verifique a configuração do Duo."]);
            }
        }

        // Pega o identificador do usuário (aqui usamos o email)
        $email = $request->input('email');

        // Gera um state único e armazena na sessão
        $state = $this->duoClient->generateState();
        Session::put('duo_state', $state);
        Session::put('duo_email', $email);

        // Redireciona para a URL de autenticação do Duo (segundo fator)
        return redirect()->away(
            $this->duoClient->createAuthUrl($email, $state)
        );
    }

    // Callback do Duo: processa a resposta do 2FA
    public function duoCallback(Request $request)
    {
        if ($request->has('error')) {
            $errorMsg = $request->input('error') . ": " . $request->input('error_description');
            Log::error($errorMsg);
            return redirect()->route('login')->withErrors(['duo' => "Erro no Duo: " . $errorMsg]);
        }

        $state = $request->input('state');
        $duoCode = $request->input('duo_code');
        $email = Session::get('duo_email');
        $savedState = Session::get('duo_state');

        if (empty($savedState) || empty($email)) {
            return redirect()->route('login')->withErrors(['duo' => 'Sessão Duo expirada. Faça login novamente.']);
        }

        if ($state !== $savedState) {
            return redirect()->route('login')->withErrors(['duo' => 'O estado retornado pelo Duo não confere com o estado salvo.']);
        }

        try {
            // Troca o duo_code pelo resultado 2FA (o método pode variar conforme a biblioteca)
            $decodedToken = $this->duoClient->exchangeAuthorizationCodeFor2FAResult($duoCode, $email);
        } catch (DuoException $e) {
            Log::error($e->getMessage());
            return redirect()->route('login')->withErrors(['duo' => 'Erro ao decodificar a resposta do Duo. Verifique a hora do dispositivo.']);
        }

        // Marca na sessão que o usuário passou no 2FA
        Session::put('duo_authenticated', true);

        return redirect()->route('dashboard')->with('message', 'Autenticação 2FA realizada com sucesso!');
    }

    // Método de logout que limpa a sessão
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login')->with('message', 'Logout realizado com sucesso!');
    }
}
