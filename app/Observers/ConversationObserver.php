<?php

namespace App\Observers;

use App\Models\Conversation;
use Illuminate\Support\Str;
use OpenAI\Client;

class ConversationObserver
{
    public function __construct(protected Client $openai) {}

    public function creating(Conversation $conversation): void
    {
        if (! $conversation->hash) {
            $conversation->hash = (string) Str::ulid();
        }

        if (! $conversation->thread_id) {
            $conversation->thread_id = $this->openai->threads()->create([])->id;
        }
    }

    public function deleted(Conversation $conversation): void
    {
        if ($conversation->thread_id) {
            $this->openai->threads()->delete($conversation->thread_id);
        }
    }
}
