<?php

namespace App\Integration\Whatsapp\Data;

use Spatie\LaravelData\Data;

class MessageTextRequestData extends Data
{
    public function __construct(
        public string $session,
        public string $chatId,
        public string $text,
        public bool $linkPreview = false,
        public bool $linkPreviewHighQuality = false
    ) {}
}
