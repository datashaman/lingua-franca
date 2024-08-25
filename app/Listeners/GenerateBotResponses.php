<?php

namespace App\Listeners;

use App\Enums\ConversationType;
use App\Events\MessageSent;
use App\Services\BotService;

class GenerateBotResponses
{
    public function __construct(
        protected BotService $botService
    ) {}

    public function handle(MessageSent $event): void
    {
        switch ($event->conversation->type) {
            case ConversationType::DirectMessage:
                if ($event->sender->isBot()) {
                    break;
                }

                $bot = $event->conversation->bots->first();

                if (! $bot) {
                    break;
                }

                $this->botService->generateResponse(
                    $bot,
                    $event->conversation
                );

                break;
        }
    }
}
