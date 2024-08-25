<?php

namespace App\Models;

use App\Contracts\Owned;
use App\Enums\ConversationType;
use App\Observers\ConversationObserver;
use App\Traits\HasOwner;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy(ConversationObserver::class)]
class Conversation extends Model implements Owned
{
    use HasFactory;
    use HasOwner;

    protected $casts = [
        'type' => ConversationType::class,
    ];

    protected $fillable = ['type', 'name', 'description'];

    public function bots(): MorphToMany
    {
        return $this->morphedByMany(Bot::class, 'member');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'member');
    }

    public function scopeHasBot(Builder $query, Bot $bot): Builder
    {
        return $query->whereHas(
            'bots',
            fn ($query) => $query->where('bots.id', $bot->id)
        );
    }

    public function scopeHasUser(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'users',
            fn ($query) => $query->where('users.id', $user->id)
        );
    }

    public function scopeHasMember(Builder $query, User|Bot $member): Builder
    {
        return $query
            ->when(
                $member instanceof Bot,
                fn ($query) => $query->hasBot($member)
            )
            ->when(
                $member instanceof User,
                fn ($query) => $query->hasUser($member)
            );
    }

    public function getRouteKeyName(): string
    {
        return 'hash';
    }
}
