<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

class CheckConnectionAgent implements Agent
{
    public function __construct(string $apikey)
    {
        config(['ai.providers.opencode.key' => $apikey]);
    }

    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a helpful assistant which help to check the model connection .';
    }
}
