<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChannelController extends Controller
{
    public function __invoke(Request $request, Channel $channel)
    {
        return Inertia::render('ChannelPage', [
            'channel' => $channel,
            'members' => $channel->members,
            'messages' => $channel->messages,
        ]);
    }
}
