<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ChannelController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('can:view-any,App\Models\Channel', only: ['index']),
            new Middleware('can:view,channel', only: ['show']),
            new Middleware('can:create,App\Models\Channel', only: ['store']),
            new Middleware('can:update,channel', only: ['update']),
            new Middleware('can:delete,channel', only: ['destroy']),
            new Middleware('can:send-message,channel', only: ['sendMessage']),
            new Middleware('can:join-channel,channel', only: ['joinChannel']),
        ];
    }

    public function index(Request $request)
    {
        $query = Channel::query()
            ->orderBy('is_system')
            ->orderBy('position')
            ->orderBy('name');

        if ($request->user()) {
            return $query->get();
        }

        return $query
            ->where('is_public', true)
            ->get();
    }

    public function store(StoreChannelRequest $request)
    {
        return Channel::create($request->validated());
    }

    public function show(Channel $channel)
    {
        return $channel;
    }

    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $channel->update($request->validated());

        return $channel;
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return response()->noContent();
    }

    public function joinChannel(Request $request, Channel $channel)
    {
        $authUser = $request->user();

        $channel->memberships()->create([
            'member_id' => $authUser->id,
            'member_type' => 'user',
        ]);

        return $channel->load('users');
    }

    public function messages(Channel $channel)
    {
        return $channel->messages()->with('sender')->latest()->get();
    }

    public function sendMessage(Request $request, Channel $channel)
    {
        $authUser = $request->user();

        $message = $channel->messages()->make([
            'content' => $request->content,
        ]);

        $message->sender()->associate($authUser);
        $message->save();

        MessageSent::dispatch($message, $authUser->translate, $authUser->locale);

        return response()->noContent();
    }
}
