<?php

namespace App\Models;

use App\Contracts\Owned;
use App\Observers\BotObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(BotObserver::class)]
class Bot extends Model implements Owned
{
    use HasFactory;

    protected $casts = [
        'properties' => 'array',
    ];

    protected $fillable = ['description', 'locale', 'instructions', 'name'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversations(): MorphToMany
    {
        return $this->morphToMany(Conversation::class, 'member')
            ->withPivot('is_muted', 'is_pinned', 'is_archived')
            ->orderBy('type')
            ->orderBy('name');
    }

    public function joinConversation(Conversation $conversation)
    {
        $conversation->bots()->attach($this->id, [
            'is_muted' => false,
            'is_pinned' => false,
            'is_archived' => false,
        ]);
    }

    public function leaveConversation(Conversation $conversation)
    {
        $conversation->bots()->detach($this->id);
    }

    public function isMember(Conversation $conversation): bool
    {
        return $conversation
            ->bots()
            ->where('bots.id', $this->id)
            ->exists();
    }

    public function isBot(): bool
    {
        return true;
    }

    public function isUser(): bool
    {
        return false;
    }

    public function getRouteKeyName(): string
    {
        return 'handle';
    }
}
