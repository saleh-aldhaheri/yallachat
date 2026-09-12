<?php

namespace App\Integration\Whatsapp\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class WAMediaS3Data extends Data
{
    public function __construct(
        #[MapInputName('Bucket')]
        public string $bucket,
        #[MapInputName('Key')]
        public string $key,
    ) {}
}
