<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Jobs\GenerateBotResponseJob;
use App\Models\Bot;

class GenerateBotResponses
{
    public function handle(MessageSent $event): void
    {
        if ($event->message->isToBot()) {
            GenerateBotResponseJob::dispatch($event->message->receiver, $event->message);
        } elseif ($event->message->isToChannel() && $event->message->receiver->members->isNotEmpty()) {
            $event->message->receiver->members->each(function ($member) use ($event) {
                if ($member instanceof Bot) {
                    logger()->info('Bot response generated', [
                        'bot' => $member->name,
                        'message' => $event->message->content,
                    ]);
                    GenerateBotResponseJob::dispatch($member, $event->message);
                }
            });
        }
    }
}
