<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelPolicy extends Policy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Channel $channel): Response
    {
        if ($channel->is_private && !$user->memberships()->where('channel_id', $channel->id)->exists()) {
            return Response::deny('You are not a member of this channel.');
        }

        return Response::allow();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Channel $channel): Response
    {
        return $user->id === $channel->user_id
            ? Response::allow()
            : Response::deny('You do not own this channel.');
    }

    public function delete(User $user, Channel $channel): Response
    {
        return $user->id === $channel->user_id
            ? Response::allow()
            : Response::deny('You do not own this channel.');
    }
}
