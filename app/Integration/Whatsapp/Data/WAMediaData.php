<?php

namespace App\Integration\Whatsapp\Data;

use Spatie\LaravelData\Data;

class WAMediaData extends Data
{
    public function __construct(
        public string $url,
        public string $mimetype,
        public string $filename,
        public WAMediaS3Data $s3,
        public ?string $error = null,
    ) {}
}
