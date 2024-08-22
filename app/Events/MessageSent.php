<?php

namespace App\Events;

use App\Models\Bot;
use App\Models\Conversation;
use App\Models\MessageTranslation;
use App\Models\User;
use Datashaman\LaravelTranslators\Facades\Translator;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateConversation;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    protected User|Bot|Conversation $sender;
    protected User|Bot|Conversation $receiver;

    public function __construct(
        public ThreadMessageResponse $message
    ) {
        $this->sender = $this->getSender();
        $this->receiver = $this->getReceiver();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateConversation($this->sender->broadcastConversation()),
            new PrivateConversation($this->receiver->broadcastConversation()),
        ];
    }

    public function broadcastWith(): array
    {
        $message = [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'role' => $this->message->role,
            'metadata' => $this->message->metadata,
            'createdAt' => $this->message->createdAt,
        ];

        if (!$this->sender->translate || $this->message->metadata['locale'] === $this->sender->locale) {
            return [
                'message' => $message,
            ];
        }

        $translation = MessageTranslation::query()
            ->where('locale', $this->sender->locale)
            ->where('message_id', $this->message->id)
            ->first();

        if ($translation) {
            $message['content'] = $translation->content;
            $message['metadata']['locale'] = $this->sender->locale;

            return [
                'message' => $message,
            ];
        }

        $message['content'] = Translator::translate($message['content'], $this->sender->locale);

        return [
            'message' => $message,
        ];
    }

    protected function getSender(): User|Bot|Conversation
    {
        return $this->getParticipant('sender');
    }

    protected function getReceiver(): User|Bot|Conversation
    {
        return $this->getParticipant('receiver');
    }

    protected function getParticipant(string $participant): User|Bot|Conversation
    {
        [$type, $id] = explode(':', $this->message->metadata[$participant]);
        $class = Relation::getMorphedModel($type);

        $object = new $class();

        return $class::where($object->getRouteKeyName(), $id)->first();
    }
}
