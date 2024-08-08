<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    public function member()
    {
        return $this->morphTo();
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
