<?php

namespace App\Services;

use App\Enums\ConversationType;
use App\Enums\MessageRole;
use App\Events\MessageSent;
use App\Models\Bot;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use OpenAI\Client;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;

class ConversationService
{
    protected User $systemUser;

    public function __construct(protected Client $openai)
    {
        $this->systemUser = User::query()
            ->where('handle', 'system')
            ->firstOrFail();
    }

    public function getDirectMessageConversation(
        Bot|User $first,
        Bot|User $second
    ): Conversation {
        $members = [$first, $second];

        $conversation = Conversation::query()
            ->where('type', ConversationType::DirectMessage)
            ->hasMember($first)
            ->hasMember($second)
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = $this->systemUser->ownedConversations()->create([
            'type' => ConversationType::DirectMessage,
        ]);

        $first->joinConversation($conversation);
        $second->joinConversation($conversation);

        return $conversation;
    }

    public function getMessages(Conversation $conversation): Collection
    {
        $cacheKey = "conversation-{$conversation->id}-messages";
        $messages = Cache::get($cacheKey, []);

        $params = [
            'order' => 'asc',
        ];

        do {
            if ($after = Arr::last($messages)?->id) {
                $params['after'] = $after;
            }

            $response = $this->openai
                ->threads()
                ->messages()
                ->list($conversation->thread_id, $params);

            $messages = array_merge($messages, $response->data);
        } while ($response->hasMore);

        Cache::put($cacheKey, $messages, now()->addHours(1));

        return collect($messages);
    }

    public function sendMessage(
        Conversation $conversation,
        Bot|User $sender,
        string $content
    ): ThreadMessageResponse {
        $message = $this->openai
            ->threads()
            ->messages()
            ->create($conversation->thread_id, [
                'content' => $content,
                'metadata' => [
                    'conversation_id' => (string) $conversation->id,
                    'sender' => "{$sender->getMorphClass()}:{$sender->getRouteKey()}",
                ],
                'role' => $sender instanceof Bot
                    ? MessageRole::Assistant
                    : MessageRole::User,
            ]);

        MessageSent::dispatch($sender, $conversation, $message);

        return $message;
    }
}
