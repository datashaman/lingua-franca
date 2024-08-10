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
        public Message $message
    ) {}

    public function broadcastOn(): array
    {
        logger()->info('Broadcasting message to ' . $this->message->receiver->broadcastChannel());

        return [
            new PrivateChannel($this->message->receiver->broadcastChannel()),
        ];
    }
}
