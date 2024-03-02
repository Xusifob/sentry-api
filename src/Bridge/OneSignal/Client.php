<?php

namespace App\Bridge\OneSignal;

use App\Bridge\OneSignal\Entity\Notification;
use Nyholm\Psr7\Factory\Psr17Factory;
use OneSignal\Config;
use OneSignal\OneSignal;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\Translation\TranslatorInterface;

class Client
{
    private readonly OneSignal $client;

    public function __construct(
        string $appId,
        string $apiKey,
        string $userAuthKey,
        private readonly TranslatorInterface $translator,
    ) {
        $config = new Config($appId, $apiKey, $userAuthKey);
        $httpClient = new Psr18Client();
        $requestFactory = $streamFactory = new Psr17Factory();

        $this->client = new OneSignal($config, $httpClient, $requestFactory, $streamFactory);
    }

    public function sendNotification(Notification $notification): void
    {
        $this->client->notifications()->add($notification->toArray($this->translator));
    }
}
