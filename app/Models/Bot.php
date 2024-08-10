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

    public function receivedMessages(): MorphMany
    {
        return $this
            ->morphMany(Message::class, 'receiver')
            ->oldest();
    }

    public function sentMessages(): MorphMany
    {
        return $this
            ->morphMany(Message::class, 'sender')
            ->oldest();
    }

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

    public function ownedChannels(): MorphMany
    {
        return $this
            ->morphMany(Channel::class, 'owner')
            ->orderBy('name');
    }

    public function joinedChannels(): HasManyThrough
    {
        return $this->hasManyThrough(Channel::class, Membership::class, 'member_id', 'id', 'id', 'channel_id')
            ->where('member_type', 'user')
            ->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'handle';
    }

    public function joinChannel(Channel $channel): Membership
    {
        return $this->memberships()->create([
            'channel_id' => $channel->id,
        ]);
    }
}
