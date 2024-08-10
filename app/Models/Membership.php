<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Membership extends Model
{
    use HasFactory;

    public function member(): MorphTo
    {
        return $this->morphTo();
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
