<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'handle',
        'locale',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_public' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function bots()
    {
        return $this
            ->hasMany(Bot::class)
            ->orderBy('name');
    }

    public function memberships()
    {
        return $this
            ->morphMany(Membership::class, 'member')
            ->latest();
    }

    public function joinedChannels(): HasManyThrough
    {
        return $this->hasManyThrough(Channel::class, Membership::class, 'member_id', 'id', 'id', 'channel_id')
            ->where('member_type', 'user')
            ->orderBy('name');
    }

    public function ownedChannels(): MorphMany
    {
        return $this
            ->morphMany(Channel::class, 'owner')
            ->orderBy('name');
    }

    public function sentMessages(): MorphMany
    {
        return $this
            ->morphMany(Message::class, 'sender')
            ->latest();

    }

    public function receivedMessages(): MorphMany
    {
        return $this
            ->morphMany(Message::class, 'receiver')
            ->latest();
    }

    public function getUnreadChannelMessageCount(Channel $channel)
    {
        return $channel
            ->messages()
            ->unreadBy($this)
            ->count();
    }

    public function getRouteKeyName()
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
