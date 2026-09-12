<?php

namespace App\Integration\Whatsapp\Exception;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;

class WhatsappException extends RequestException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        ?Response $response = null,
    ) {
        if ($response instanceof Response) {
            parent::__construct($response, $message !== '' ? $message : null, $code, $previous);

            return;
        }

        \Exception::__construct($message, $code, $previous);
    }
}
