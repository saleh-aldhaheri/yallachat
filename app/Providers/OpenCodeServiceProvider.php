<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\RequestInterface;

class OpenCodeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // add Session ID and change the user agent to CLI from Guzzle
        Http::globalRequestMiddleware(function (RequestInterface $request) {
            $uri = (string) $request->getUri();
            if (str_contains($uri, 'opencode.ai')) {
                $request = $request
                    ->withHeader('User-Agent', 'opencode/latest/1.3.15/cli')
                    ->withHeader('x-opencode-session', 'session-gt-' . time());
            }
            return $request;
        });
    }
}
