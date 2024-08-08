<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    protected $casts = [
        'properties' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this
            ->hasMany(Message::class)
            ->latest();
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

    public function joinedChannels(): HasManyThrough
    {
        return $this->hasManyThrough(Channel::class, Membership::class, 'member_id', 'id', 'id', 'channel_id')
                    ->where('member_type', 'bot')
                    ->orderBy('name');
    }
}
