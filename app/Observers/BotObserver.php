<?php

namespace App\Observers;

use App\Models\Bot;
use OpenAI\Client;

class BotObserver
{
    public function __construct(protected Client $client) {}

    public function creating(Bot $bot): void
    {
        if (! $bot->assistant_id) {
            $bot->assistant_id = $this->client->assistants()->create(
                $bot->only(['name', 'instructions', 'description', 'model'])
            )->id;
        }
    }

    public function updated(Bot $bot): void
    {
        if ($bot->assistant_id && $bot->isDirty(['name', 'instructions', 'description'])) {
            $this->client->assistants()->modify(
                $bot->assistant_id,
                $bot->only(['name', 'instructions', 'description', 'model'])
            );
        }
    }

    public function deleted(Bot $bot): void
    {
        if ($bot->assistant_id) {
            $this->client->assistants()->delete($bot->assistant_id);
        }
    }
}
