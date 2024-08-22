<?php

namespace App\Models;

use App\Enums\ConversationType;
use App\Interfaces\Threadable;
use App\Observers\CreateThread;
use App\Traits\HasThread;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[ObservedBy(CreateThread::class)]
class Conversation extends Model implements Threadable
{
    use HasFactory;
    use HasThread;
    use Notifiable;

    protected $casts = [
        'type' => ConversationType::class,
    ];

    protected $fillable = [
        'name',
        'type',
    ];

    protected $appends = [
        'locale_count',
        'member_count',
    ];

    public static function between(array $members, ConversationType $type): ?Conversation
    {
        $members = collect($members);

        return static::whereHas(
            'memberships',
            fn ($query) => $query
                ->where(
                    fn ($query) => $members
                        ->each(
                            fn ($member) => $query
                                ->orWhere(
                                    fn ($query) => $query
                                        ->where('member_id', $member['id'])
                                        ->where('member_type', $member['type'])
                                )
                        )
                )
                ->groupBy('conversation_id')
                ->havingRaw('COUNT(DISTINCT member_id) = ?', [count($members)])
        )->where('type', $type)->first();
    }

    protected static function booted()
    {
        static::creating(function (Conversation $conversation) {
            if (! $conversation->hash) {
                $conversation->hash = (string) Str::ulid();
            }

            if (! $conversation->slug && $conversation->name) {
                $conversation->slug = Str::slug($conversation->name);
            }

            if (! $conversation->user_id) {
                $conversation->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
                            ->where('conversation_id', $this->id)
                    )
                    ->groupBy('locale')
                    ->pluck('count', 'locale');

                $bots = Bot::query()
                    ->selectRaw('locale, count(*) as count')
                    ->whereHas(
                        'memberships', fn ($q) => $q
                            ->where('member_type', 'bot')
                            ->where('conversation_id', $this->id)
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

    public function getRouteKeyName(): string
    {
        return 'hash';
    }
}
