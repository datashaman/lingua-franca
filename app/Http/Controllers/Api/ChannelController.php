<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Channel::all()
            ->filter(fn ($channel)  => $user->can('view', $channel))
            ->map(
                function ($channel) use ($user) {
                    $channel->is_member = $channel
                            ->memberships()
                            ->where('member_id', $user->id)
                            ->where('member_type', 'user')
                            ->exists();

                    return $channel;
                }
            );
    }

    public function store(Request $request)
    {
        return Channel::create($request->all());
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
