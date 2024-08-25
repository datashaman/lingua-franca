<?php

namespace App\Events;

use App\Models\Bot;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Bot|User $sender,
        public Conversation $conversation,
        public ThreadMessageResponse $message
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel($this->conversation->broadcastChannel());
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->toArray(),
        ];
    }
}
