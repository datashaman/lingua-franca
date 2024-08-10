<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChannelController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Channel $channel)
    {
        $messages = $channel->notifications;
        dump($messages);

        return Inertia::render('ChannelPage', [
            'channel' => $channel,
            'members' => $channel->members,
            'messages' => $channel->notifications,
        ]);
    }
}
