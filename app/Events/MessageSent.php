<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Message $message,
        public bool $translate = false,
        public string $locale = 'en'
    ) {
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel($this->message->sender->broadcastChannel()),
            new PrivateChannel($this->message->receiver->broadcastChannel()),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->toArray(
                $this->translate,
                $this->locale
            ),
        ];
    }
}
