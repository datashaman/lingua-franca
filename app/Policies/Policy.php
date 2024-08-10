<?php

namespace App\Policies;

use App\Models\User;

class Policy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_admin) {
            return true;
        }

        return null;
    }
}
