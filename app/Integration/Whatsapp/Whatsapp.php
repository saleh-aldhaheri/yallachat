<?php

namespace App\Integration\Whatsapp;

use App\Integration\Whatsapp\Resource\Send;
use Saloon\Http\Connector;

class Whatsapp extends Connector
{
    public ?int $tries = 3;

    public ?int $retryInterval = 3;

    public ?bool $useExponentialBackoff = true;

    public function __construct(
        private readonly string $apiKey
    ) {}

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Api-Key' => $this->apiKey,
        ];
    }

    public function resolveBaseUrl(): string
    {
        return 'http://localhost:3000/api/';
    }

    public function sendText(string $session, string $chatId, string $text): mixed
    {
        return (new Send($this))->sendText($session, $chatId, $text);
    }
}
