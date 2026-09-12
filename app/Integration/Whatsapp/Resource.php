<?php

namespace App\Integration\Whatsapp;

class Resource
{
    public function __construct(
        protected readonly Whatsapp $connector
    ) {}
}
