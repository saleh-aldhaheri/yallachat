<?php

namespace App\Integration\Whatsapp\Request;

use App\Integration\Whatsapp\Exception\WhatsappException;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class WhatsappRequest extends Request
{
    public function createDtoFromResponse(Response $response): mixed
    {
        try {
            return $this->toDto($response);
        } catch (\Throwable $exception) {
            throw new WhatsappException($exception->getMessage());
        }
    }

    abstract public function toDto(Response $response);
}
