<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
        'translate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

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
            'password' => 'hashed',
            'translate' => 'boolean',
        ];
    }

    public function bots(): HasMany
    {
        return $this->hasMany(Bot::class, 'owner_id')->orderBy('name');
    }

    public function conversations(): MorphToMany
    {
        return $this->morphToMany(Conversation::class, 'member')
            ->withPivot('is_muted', 'is_pinned', 'is_archived')
            ->orderBy('type')
            ->orderBy('name');
    }

    public function ownedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'owner_id')
            ->orderBy('type')
            ->orderBy('name');
    }

    public function joinConversation(Conversation $conversation)
    {
        $conversation->users()->attach($this->id, [
            'is_muted' => false,
            'is_pinned' => false,
            'is_archived' => false,
        ]);
    }

    public function leaveConversation(Conversation $conversation)
    {
        $conversation->users()->detach($this->id);
    }

    public function isMember(Conversation $conversation): bool
    {
        return $conversation
            ->users()
            ->where('users.id', $this->id)
            ->exists();
    }

    public function isOwner(Owned $owned): bool
    {
        return $owned->owner_id === $this->id;
    }

    public function isBot(): bool
    {
        return false;
    }

    public function isUser(): bool
    {
        return true;
    }

    public function getRouteKeyName()
    {
        return 'handle';
    }
}
