<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConversationPolicy extends Policy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Conversation $conversation): Response
    {
        if ($conversation->is_public) {
            return Response::allow();
        }

        if ($user && $user->memberships()->where('conversation_id', $conversation->id)->exists()) {
            return Response::allow();
        }

        return Response::deny('You are not a member of this conversation.');
    }

    public function create(?User $user): bool
    {
        return (bool) $user;
    }

    public function update(User $user, Conversation $conversation): Response
    {
        return $user->id === $conversation->user_id
            ? Response::allow()
            : Response::deny('You do not own this conversation.');
    }

    public function delete(User $user, Conversation $conversation): Response
    {
        return $user->id === $conversation->user_id
            ? Response::allow()
            : Response::deny('You do not own this conversation.');
    }

    public function sendMessage(User $user, Conversation $conversation): Response
    {
        return $user->memberships()->where('conversation_id', $conversation->id)->exists()
            ? Response::allow()
            : Response::deny('You are not a member of this conversation.');
    }

    public function joinConversation(?User $user, Conversation $conversation): Response
    {
        if (! $user) {
            return Response::deny('You must be logged in to join a conversation.');
        }

        if ($user->memberships()->where('conversation_id', $conversation->id)->exists()) {
            return Response::deny('You are already a member of this conversation.');
        }

        return Response::allow();
    }
}
