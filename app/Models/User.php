<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'is_public',
        'locale',
        'name',
        'password',
        'translate',
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
            'translate' => 'boolean',
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

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class)
            ->orderBy('name');
    }

    public function joinedConversations(): HasManyThrough
    {
        return $this->hasManyThrough(Conversation::class, Membership::class, 'member_id', 'id', 'id', 'conversation_id')
            ->where('member_type', 'user')
            ->orderBy('name');
    }

    public function getRouteKeyName()
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
