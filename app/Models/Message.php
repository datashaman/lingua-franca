<?php

namespace App\Models;

use Datashaman\LaravelTranslators\Facades\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OpenAI\Client;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'receiver_id',
        'receiver_type',
        'sender_id',
        'sender_type',
    ];

    protected $with = [
        'receiver',
        'sender',
    ];

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function receiver(): MorphTo
    {
        return $this->morphTo();
    }

    public function messageTranslations(): HasMany
    {
        return $this->hasMany(MessageTranslation::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => auth()->user()?->translate
                ? $this->translate(auth()->user()->locale)
                : $this->content,
            'sender' => $this->getFields($this->sender),
            'receiver' => $this->getFields($this->receiver),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function isFromUser(): bool
    {
        return $this->sender_type === 'user';
    }

    public function isFromBot(): bool
    {
        return $this->sender_type === 'bot';
    }

    public function isToUser(): bool
    {
        return $this->receiver_type === 'user';
    }

    public function isToBot(): bool
    {
        return $this->receiver_type === 'bot';
    }

    public function isToChannel(): bool
    {
        return $this->receiver_type === 'channel';
    }

    public function scopeBetween(Builder $query, Bot|User $sender, Bot|User $receiver): Builder
    {
        return $query
            ->where(
                fn ($query) => $query
                    ->where(
                        fn ($query) => $query
                            ->where('sender_id', $sender->id)
                            ->where('sender_type', $sender->getMorphClass())
                            ->where('receiver_id', $receiver->id)
                            ->where('receiver_type', $receiver->getMorphClass())
                    )->orWhere(
                        fn ($query) => $query
                            ->where('sender_id', $receiver->id)
                            ->where('sender_type', $receiver->getMorphClass())
                            ->where('receiver_id', $sender->id)
                            ->where('receiver_type', $sender->getMorphClass())
                    )
            );
    }

    public function translate(string $locale): string
    {
        $translation = $this
            ->messageTranslations()
            ->where('locale', $locale)
            ->first();

        if ($translation) {
            return $translation->content;
        }

        $content = $this->createTranslation($locale);

        return $this->saveTranslation($locale, $content);
    }

    protected function createTranslation(string $locale): string
    {
        return Translator::translate([$this->content], $locale)[0];
    }

    public function saveTranslation(string $locale, string $content): string
    {
        if ($this->id) {
            $translation = MessageTranslation::firstOrCreate([
                'message_id' => $this->id,
                'locale' => $locale,
            ], [
                'content' => $content,
            ]);

            return $translation->content;
        }

        return $content;
    }

    protected function getFields(Model $model): array
    {
        return array_merge(
            $model->only([
                'id',
                'handle',
                'name',
            ]),
            [
                'type' => $model->getMorphClass(),
            ]
        );
    }
}
