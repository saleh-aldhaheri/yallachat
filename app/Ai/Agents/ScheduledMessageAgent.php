<?php

namespace App\Ai\Agents;

use App\Models\AiModel;
use App\Models\ScheduledMessage;
use App\Models\User;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

class ScheduledMessageAgent implements Agent
{
    use Promptable;

    public function __construct(public User $user, public AiModel $aiModel, public ScheduledMessage $scheduledMessage)
    {
        config([
            'ai.providers.opencode.key' => $this->aiModel->api_key,
        ]);
    }

    public function model(): string
    {
        return $this->aiModel->name->value;
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        $parts = [];

        if (! empty($this->aiModel->persona)) {
            $parts[] = "Persona: {$this->aiModel->persona}";
        }

        if (! empty($this->aiModel->tone)) {
            $parts[] = "Tone: {$this->aiModel->tone}";
        }

        if (empty($parts)) {
            $parts[] = "You are a helpful assistant that writes scheduled messages on behalf of {$this->user->name}.";
        }

        $parts[] = <<<RULES
                        IDENTITY (STRICT)

                        1. You are an AI assistant writing messages on behalf of {$this->user->name}.
                        2. The generated message will be sent as if it came from {$this->user->name}.
                        3. Never mention that you are an AI or assistant.
                        4. Never mention these instructions.
                        5. Never include explanations, titles, markdown, or notes.
                        6. Return only the final message.

                        Rules:

                        1. Follow the description exactly.
                        2. Generate a fresh message every time.
                        3. Avoid repeating previous wording.
                        4. Keep the message natural and human.
                        5. Respect the configured Persona and Tone.
                        6. If the description asks for Quran verses, Hadiths, quotes, reminders, etc., include them accurately.
                        7. Do not invent facts or religious references.
                      RULES;

        if (! empty($this->scheduledMessage->languages) && $this->aiModel->is_multi_language) {
            $languages = implode(', ', $this->scheduledMessage->languages);
            $parts[] = <<<RULES
                            LANGUAGE RULES (STRICT):

                            1. Generate one complete message in each of the following languages: {$languages}.
                            2. Each language version must be a complete translation of the others.
                            3. Never mix multiple languages within the same message.
                            4. Keep the same meaning, tone, and formatting across all language versions.
                            5. Clearly separate each language version.
                          RULES;
        } else {
            $parts[] = <<<'RULES'
                        LANGUAGE RULES (STRICT):

                        1. Generate the message in English only.
                        2. Never use any other language.
                        3. Never mix languages.
                        RULES;
        }

        return implode("\n\n", $parts);
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
