<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
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
