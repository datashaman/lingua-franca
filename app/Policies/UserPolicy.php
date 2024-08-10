<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy extends Policy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, User $model): bool
    {
        return $model->is_public || $user?->id === $model->id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $model): Response
    {
        return $user->id === $model->id
            ? Response::allow()
            : Response::deny('You do not own this user.');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
