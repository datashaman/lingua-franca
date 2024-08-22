<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Jobs\GenerateBotResponseJob;
use App\Models\Bot;
use App\Models\Conversation;
use Illuminate\Support\Str;

class GenerateBotResponses
{
    public function handle(MessageSent $event): void
    {
        if ($this->isToBot($event)) {
            $id = Str::after($event->message->metadata['receiver'], 'bot:');
            $bot = Bot::findOrFail($id);

            GenerateBotResponseJob::dispatch(
                $bot,
                $event->message,
                $event->translate,
                $event->locale,
            );
        } elseif ($conversation = $this->isToConversationWithMembers($event)) {
            $conversation->members->each(function ($member) use ($event) {
                if ($member instanceof Bot) {
                    logger()->info('Bot response generated', [
                        'bot' => $member->handle,
                        'message' => $event->message->content,
                    ]);
                    GenerateBotResponseJob::dispatch(
                        $member,
                        $event->message,
                        $event->translate,
                        $event->locale,
                    );
                }
            });
        }
    }

    protected function isToBot($event): bool
    {
        return Str::startsWith($event->message->metadata['receiver'], ['bot:']);
    }

    protected function isToConversationWithMembers($event): null|Conversation
    {
        if (! Str::startsWith($event->message->metadata['receiver'], ['conversation:'])) {
            return null;
        }

        [$type, $slug] = explode(':', $event->message->metadata['receiver']);

        $conversation = Conversation::where('slug', $slug)->firstOrFail();

        if ($conversation->members->isEmpty()) {
            return null;
        }

        return $conversation;
    }
}
