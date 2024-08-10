<?php

use App\Models\Channel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Models.Channel.{id}', function ($user, $id) {
    $channel = Channel::find($id);

    return $user->can('view', $channel);
});
