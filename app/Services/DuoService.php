<?php

namespace App\Services;

use Duo\DuoUniversal\Client;
use Duo\DuoUniversal\DuoException;
use Illuminate\Support\Facades\Log;

class DuoService
{
    protected Client $duoClient;
    protected string $failmode;

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
            Log::info("DuoService: Cliente Duo configurado com sucesso.");
        } catch (DuoException $e) {
            Log::error("DuoService: Erro de configuração do Duo: " . $e->getMessage());
            throw new \RuntimeException("Erro de configuração do Duo: " . $e->getMessage(), $e->getCode(), $e);
        }

        // Define o failmode (usando um valor padrão se não existir)
        $this->failmode = strtoupper(config('duo.failmode', 'CLOSED'));
        Log::info("DuoService: Failmode configurado: " . $this->failmode);
    }

    /**
     * Executa o health check do Duo.
     *
     * @throws DuoException
     */
    public function healthCheck(): void
    {
        $this->duoClient->healthCheck();
    }

    /**
     * Gera um estado único para o fluxo de 2FA.
     *
     * @return string
     */
    public function generateState(): string
    {
        return $this->duoClient->generateState();
    }

    /**
     * Cria a URL para a autenticação 2FA.
     *
     * @param string $username
     * @param string $state
     * @return string
     */
    public function createAuthUrl(string $username, string $state): string
    {
        return $this->duoClient->createAuthUrl($username, $state);
    }

    /**
     * Troca o código de autorização por um token 2FA.
     *
     * @param string $duoCode
     * @param string $username
     * @return array
     * @throws DuoException
     */
    public function exchangeAuthorizationCode(string $duoCode, string $username): array
    {
        return $this->duoClient->exchangeAuthorizationCodeFor2FAResult($duoCode, $username);
    }

    /**
     * Retorna o failmode configurado.
     *
     * @return string
     */
    public function getFailmode(): string
    {
        return $this->failmode;
    }
}
