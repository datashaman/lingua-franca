<?php

namespace App\Policies;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BotPolicy extends Policy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Bot $bot): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, Bot $bot): Response
    {
        return $user->id === $bot->user_id
            ? Response::allow()
            : Response::deny('You do not own this bot.');
    }

    public function delete(User $user, Bot $bot): Response
    {
        return $user->id === $bot->user_id
            ? Response::allow()
            : Response::deny('You do not own this bot.');
    }

    public function sendMessage(User $user, Bot $bot): Response
    {
        return true;
    }
}
