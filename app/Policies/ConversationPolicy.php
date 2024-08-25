<?php

namespace App\Policies;

use App\Enums\ConversationType;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conversation $conversation): Response
    {
        if ($conversation->type === ConversationType::PublicChannel) {
            return Response::allow();
        }

        if ($user->isMember($conversation)) {
            return Response::allow();
        }

        return Response::deny('You are not a member of this conversation.');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Conversation $conversation): Response
    {
        return $user->isOwner($conversation)
            ? Response::allow()
            : Response::deny('You do not own this conversation.');
    }

    public function delete(User $user, Conversation $conversation): Response
    {
        return $user->isOwner($conversation)
            ? Response::allow()
            : Response::deny('You do not own this conversation.');
    }

    public function sendMessage(
        User $user,
        Conversation $conversation
    ): Response {
        return $user->isMember($conversation)
            ? Response::allow()
            : Response::deny('You are not a member of this conversation.');
    }

    public function invite(User $user, Conversation $conversation): Response
    {
        if ($conversation->type === ConversationType::DirectMessage) {
            return Response::deny(
                'You cannot invite users to a direct message conversation.'
            );
        }

        return $user->isMember($conversation)
            ? Response::allow()
            : Response::deny('You are not a member of this conversation.');
    }

    public function join(User $user, Conversation $conversation): Response
    {
        if ($conversation->type === ConversationType::DirectMessage) {
            return Response::deny(
                'You cannot join a direct message conversation.'
            );
        }

        if ($conversation->type === ConversationType::PrivateChannel) {
            return Response::deny('You cannot join a private conversation.');
        }

        if ($user->isMember($conversation)) {
            return Response::deny(
                'You are already a member of this conversation.'
            );
        }

        return Response::allow();
    }

    public function leave(User $user, Conversation $conversation): Response
    {
        if ($conversation->type === ConversationType::DirectMessage) {
            return Response::deny(
                'You cannot leave a direct message conversation.'
            );
        }

        if (! $user->isMember($conversation)) {
            return Response::deny(
                'You are not a member of this conversation.'
            );
        }

        return Response::allow();
    }
}
