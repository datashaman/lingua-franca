<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChannelPolicy extends Policy
{
    public function viewAny(null|User $user): bool
    {
        return true;
    }

    public function view(null|User $user, Channel $channel): Response
    {
        if ($channel->is_public) {
            return Response::allow();
        }

        if ($user && $user->memberships()->where('channel_id', $channel->id)->exists()) {
            return Response::allow();
        }

        return Response::deny('You are not a member of this channel.');
    }

    public function create(null|User $user): bool
    {
        return (bool) $user;
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

    public function sendMessage(User $user, Channel $channel): Response
    {
        return $user->memberships()->where('channel_id', $channel->id)->exists()
            ? Response::allow()
            : Response::deny('You are not a member of this channel.');
    }

    public function joinChannel(null|User $user, Channel $channel): Response
    {
        if (! $user) {
            return Response::deny('You must be logged in to join a channel.');
        }

        if ($user->memberships()->where('channel_id', $channel->id)->exists()) {
            return Response::deny('You are already a member of this channel.');
        }

        return Response::allow();
    }
}
