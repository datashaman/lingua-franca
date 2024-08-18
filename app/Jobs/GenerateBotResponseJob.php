<?php

namespace App\Jobs;

use App\Events\MessageSent;
use App\Models\Bot;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use OpenAI\Client;

class GenerateBotResponseJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Bot $bot,
        protected Message $message,
        protected bool $translate = false,
        protected string $locale = 'en',
    ) {}

    public function handle(Client $openai): void
    {
        switch ($this->message->receiver_type) {
            case 'channel':
                $this->generateResponseTo($openai, $this->message->receiver);
                break;
            default:
                $this->generateResponseTo($openai, $this->message->sender);
                break;
        }
    }

    protected function generateResponseTo(Client $openai, User|Bot|Channel $receiver): void
    {
        if ($this->message->isFromBot()) {
            return;
        }

        $receiverType = $receiver->getMorphClass();

        $messages = match ($receiverType) {
            'channel' => $receiver->messages,
            'bot', 'user' => Message::query()
                ->between($this->bot, $receiver)
                ->get(),
        };

        $messages = $messages->map(fn (Message $message) => [
            'role' => ($message->sender_id === $this->bot->id && $message->sender_type === $this->bot->getMorphClass())
                ? 'assistant'
                : 'user',
            'content' => $message->content,
        ]);

        $messages->prepend([
            'role' => 'system',
            'content' => $this->bot->instructions,
        ]);

        if ($messages->count() === 1) {
            $messages->prepend([
                'role' => 'system',
                'content' => "Please say 'Hello' in your language to start the conversation.",
            ]);
        }

        $response = $openai->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => $messages->all(),
        ]);

        $content = $response->choices[0]->message->content;

        $message = Message::create([
            'bot_id' => $this->bot->id,
            'receiver_type' => $receiver->getMorphClass(),
            'receiver_id' => $receiver->id,
            'sender_id' => $this->bot->id,
            'sender_type' => $this->bot->getMorphClass(),
            'content' => $content,
        ]);

        MessageSent::dispatch($message, $this->translate, $this->locale);
    }
}
