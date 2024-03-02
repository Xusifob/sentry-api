<?php

namespace App\Bridge\OneSignal\Entity;

use App\Entity\Device;
use App\Utils\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class Notification
{
    public const TYPE_DAILY_REMINDER = 'daily_reminder';

    /**
     * @var Device[]
     */
    public array $devices = [];

    public string $type;

    public string $web_url;

    public array $variables = [];

    public function toArray(TranslatorInterface $translator): array
    {
        return [
            'headings' => [
                'en' => $translator->trans("{$this->type}.title", Translator::addBracketToParameters($this->variables), 'notifications'),
            ],
            'contents' => [
                'en' => $translator->trans("{$this->type}.content", Translator::addBracketToParameters($this->variables), 'notifications'),
            ],
            'include_player_ids' => array_map(fn (Device $device) => $device->token, $this->devices),
            'web_url' => $this->web_url,
        ];
    }

    public function addDevice(Device $device): void
    {
        $this->devices[] = $device;
    }
}
