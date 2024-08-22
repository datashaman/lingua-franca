<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Bot extends Model
{
    use HasFactory;

    protected $casts = [
        'is_public' => 'boolean',
        'properties' => 'array',
    ];

    protected $fillable = [
        'description',
        'locale',
        'instructions',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function memberships(): MorphMany
    {
        return $this
            ->morphMany(Membership::class, 'member')
            ->latest();
    }

    public function conversations(): HasManyThrough
    {
        return $this->hasManyThrough(Conversation::class, Membership::class, 'member_id', 'id', 'id', 'conversation_id')
            ->where('member_type', 'user')
            ->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'handle';
    }

    public function joinConversation(Conversation $conversation): Membership
    {
        return $this->memberships()->create([
            'conversation_id' => $conversation->id,
        ]);
    }
}
