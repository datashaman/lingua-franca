<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $casts = [
        'is_private' => 'boolean'
    ];

    public function messages()
    {
        return $this
            ->hasMany(Message::class)
            ->oldest();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memberships()
    {
        return $this
            ->morphMany(Membership::class, 'member')
            ->latest();

    }
}
