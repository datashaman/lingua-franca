<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory;

    protected $casts = [
        'is_private' => 'boolean'
    ];

    protected $fillable = [
        'name',
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

    public function messages(): MorphMany
    {
        return $this
            ->morphMany(Message::class, 'receiver')
            ->oldest();
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function memberships(): MorphMany
    {
        return $this
            ->morphMany(Membership::class, 'member')
            ->latest();

    }
}
