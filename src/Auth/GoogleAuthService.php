<?php

namespace App\Auth;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleAuthService
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $stateCachePool
    ) {
    }

    public function generateAndStoreState(): string
    {
        $state = bin2hex(random_bytes(16));

        $this->stateCachePool->get($state, function (ItemInterface $item) use ($state) {
            // Store the state in cache for 5 minutes
            $item->expiresAfter(60 * 5);

            return $state;
        });

        return $state;
    }

    public function validateState(string $state): bool
    {
        if (!$state) {
            return false;
        }

        /** @var string|null $cachedState */
        $cachedState = $this->stateCachePool->get($state, fn () => null);

        if (null === $cachedState) {
            return false;
        }

        $this->stateCachePool->delete($state);

        return true;
    }

    public function getAuthUrl(): string
    {
        $state = $this->generateAndStoreState();

        return "https://accounts.google.com/o/oauth2/v2/auth?scope=email%20profile&access_type=offline&include_granted_scopes=true&response_type=code&state={$state}&redirect_uri={$this->redirectUrl}&client_id={$this->clientId}";
    }

    public function getToken(string $code): array
    {
        $response = $this->httpClient->request('POST', 'https://oauth2.googleapis.com/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'code' => $code,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUrl,
                'grant_type' => 'authorization_code',
            ],
        ]);

        return $response->toArray();
    }

    public function getUserInfos(string $accessToken): array
    {
        $response = $this->httpClient->request('GET', 'https://www.googleapis.com/oauth2/v1/userinfo', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ]);

        return $response->toArray();
    }
}
