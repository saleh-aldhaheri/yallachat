<?php

namespace App\Integration\Whatsapp\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class WAReplyToData extends Data
{
    public function __construct(
        public string $id,
        public string $participant,
        public string $body,
        public bool $hasMedia,
        public ?WAMediaData $media = null,
        #[MapInputName('_data')]
        public ?array $data = null,
    ) {}
}
