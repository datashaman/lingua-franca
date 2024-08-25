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
        public ThreadMessageResponse $message
    ) {
        $this->sender = $this->getSender();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel($this->conversation->broadcastChannel())];
    }

    public function broadcastWith(): array
    {
        if (! $this->sender->translate) {
            return [
                'message' => $this->message,
            ];
        }

        $translation = MessageTranslation::query()
            ->where('locale', $this->sender->locale)
            ->where('message_id', $this->message->id)
            ->first();

        if ($translation) {
            $this->message->content[0]->text->value = $translation->content;
            $this->message->metadata['locale'] = $this->sender->locale;

            return [
                'message' => $message,
            ];
        }

        $this->message->content[0]->text->value = Translator::translate(
            [$this->message->content[0]->text->value],
            $this->sender->locale
        )[0];

        return [
            'message' => $this->message,
        ];
    }

    protected function getSender(): User|Bot|Conversation
    {
        return $this->getParticipant('sender');
    }

    protected function getParticipant(
        string $participant
    ): User|Bot|Conversation {
        [$type, $id] = explode(':', $this->message->metadata[$participant]);
        $class = Relation::getMorphedModel($type);

        $object = new $class;

        return $class::where($object->getRouteKeyName(), $id)->first();
    }
}
