<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MembershipPolicy extends Policy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Membership $membership): Response
    {
        if ($membership->channel->is_public) {
            return Response::allow();
        }

        return $user->id === $membership->member_id && $membership->member_type === 'user'
            ? Response::allow()
            : Response::deny('You are not a member of this channel.');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Membership $membership): Response
    {
        return $user->id === $membership->member_id && $membership->member_type === 'user'
            ? Response::allow()
            : Response::deny('You are not a member of this channel.');
    }

    public function delete(User $user, Membership $membership): Response
    {
        return $user->id === $membership->member_id && $membership->member_type === 'user'
            ? Response::allow()
            : Response::deny('You are not a member of this channel.');
    }
}
