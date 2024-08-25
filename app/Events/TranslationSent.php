<?php

namespace App\Events;

use App\Models\Bot;
use App\Models\Conversation;
use App\Models\MessageTranslation;
use App\Models\User;
use Datashaman\LaravelTranslators\Facades\Translator;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;

class TranslationSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    protected User|Bot $sender;

    public function __construct(
        public Conversation $conversation,
        public ThreadMessageResponse $message,
        public string $locale,
    ) {
    }

    public function broadcastAs(): string
    {
        return static::class . ".{$this->locale}";
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel($this->conversation->broadcastChannel())];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
