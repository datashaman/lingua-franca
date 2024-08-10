<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory;
    use Notifiable;

    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected $fillable = [
        'name',
    ];

    protected $appends = [
        'locale_count',
        'member_count',
        'unread_message_count',
    ];

    protected static function booted()
    {
        static::creating(function (Channel $channel) {
            if (!$channel->slug) {
                $channel->slug = Str::slug($channel->name);
            }

            if (!$channel->owner_id) {
                $channel->owner_id = auth()->id();
                $channel->owner_type = 'user';
            }
        });
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function memberships(): HasMany
    {
        return $this
            ->hasMany(Membership::class)
            ->latest();

    }

    public function members(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->memberships()->get()->pluck('member')
        );
    }

    public function memberCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->memberships()->count()
        );
    }

    public function localeCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $users = User::query()
                    ->selectRaw('locale, count(*) as count')
                    ->whereHas(
                        'memberships', fn ($q) => $q
                            ->where('member_type', 'user')
                            ->where('channel_id', $this->id)
                    )
                    ->groupBy('locale')
                    ->pluck('count', 'locale');

                $bots = Bot::query()
                    ->selectRaw('locale, count(*) as count')
                    ->whereHas(
                        'memberships', fn ($q) => $q
                            ->where('member_type', 'bot')
                            ->where('channel_id', $this->id)
                    )
                    ->groupBy('locale')
                    ->pluck('count', 'locale');

                return $users
                    ->keys()
                    ->merge($bots->keys())
                    ->unique()
                    ->count();
            }
        );
    }

    public function unreadMessageCount(): Attribute
    {
        return Attribute::make(
            get: fn () => 0
        );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
