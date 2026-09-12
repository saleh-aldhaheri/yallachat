<?php

namespace App\Integration\Whatsapp\Data;

use Spatie\LaravelData\Data;

class WALocationData extends Data
{
    public function __construct(
        public string $latitude,
        public string $longitude,
        public bool $live,
        public ?string $name = null,
        public ?string $address = null,
        public ?string $url = null,
        public ?string $description = null,
        public ?string $thumbnail = null,
    ) {}
}
