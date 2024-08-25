<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{id}.*', function ($user, $id) {
    $conversation = Conversation::find($id);

    return $user->can('view', $conversation);
});
