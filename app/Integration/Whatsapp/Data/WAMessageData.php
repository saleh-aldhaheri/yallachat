<?php

namespace App\Integration\Whatsapp\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class WAMessageData extends Data
{
    /**
     * @param  array<string>  $vCards
     */
    public function __construct(
        public string $id,
        public int $timestamp,
        public string $from,
        public bool $fromMe,
        public string $source,
        public string $to,
        public string $participant,
        public string $body,
        public bool $hasMedia,
        public int $ack,
        public string $ackName,
        public string $author,
        public array $vCards,
        #[MapInputName('_data')]
        public array $data,
        public ?WAMediaData $media = null,
        /** @deprecated */
        public ?string $mediaUrl = null,
        public ?WALocationData $location = null,
        public ?WAReplyToData $replyTo = null,
    ) {}
}
