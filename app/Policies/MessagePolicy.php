<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy extends Policy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Message $message): Response
    {
        if ($message->channel) {
            return $user->memberships()->where('channel_id', $message->channel->id)->exists()
                ? Response::allow()
                : Response::deny('You are not a member of this channel.');
        }

        $allowed = $user->id === $message->receiver_id && $message->receiver_type === 'user'
            || $user->id === $message->sender_id && $message->sender_type === 'user';

        return $allowed
            ? Response::allow()
            : Response::deny('You are not the recipient of this message.');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Message $message): Response
    {
        return $user->id === $message->sender_id && $message->sender_type === 'user'
            ? Response::allow()
            : Response::deny('You are not the sender of this message.');
    }

    public function delete(User $user, Message $message): Response
    {
        return $user->id === $message->sender_id && $message->sender_type === 'user'
            ? Response::allow()
            : Response::deny('You are not the sender of this message.');
    }
}
