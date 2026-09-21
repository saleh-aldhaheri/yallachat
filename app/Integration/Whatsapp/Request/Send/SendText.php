<?php

namespace App\Integration\Whatsapp\Request\Send;

use App\Integration\Whatsapp\Data\MessageTextRequestData;
use App\Integration\Whatsapp\Data\WAMessageData;
use App\Integration\Whatsapp\Request\WhatsappRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Spatie\LaravelData\DataCollection;

class SendText extends WhatsappRequest implements HasBody
{
    use HasJsonBody;

    public Method $method = Method::POST;

    public function __construct(
        private readonly MessageTextRequestData $messageTextRequestData
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/sendText';
    }

    protected function defaultBody(): array
    {
        return $this->messageTextRequestData->toArray();
    }

    public function toDto(Response $response): DataCollection
    {
        if ($response->failed()) {
            throw new \Exception('WhatsApp API Request failed.');
        }

        $json = $response->json();

        if (! empty($json['error'])) {
            throw new \Exception(is_array($json['error']) ? json_encode($json['error']) : $json['error']);
        }

        $data = data_get($json, 'data', $json);

        $items = isset($data['id']) ? [$data] : $data;

        return WAMessageData::collect($items, DataCollection::class);
    }
}
