<?php

namespace App\Integration\Whatsapp\Resource;

use App\Integration\Whatsapp\Data\MessageTextRequestData;
use App\Integration\Whatsapp\Request\Send\SendText;
use App\Integration\Whatsapp\Resource;

class Send extends Resource
{
    public function sendText(string $session, string $chatId, string $text): mixed
    {
        return $this->connector->send(new SendText(new MessageTextRequestData($session, $chatId, $text)));
    }
}
