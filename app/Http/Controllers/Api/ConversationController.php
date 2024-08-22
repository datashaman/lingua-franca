<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use OpenAI\Client;

class ConversationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('can:view-any,App\Models\Conversation', only: ['index']),
            new Middleware('can:view,conversation', only: ['show']),
            new Middleware('can:create,App\Models\Conversation', only: ['store']),
            new Middleware('can:update,conversation', only: ['update']),
            new Middleware('can:delete,conversation', only: ['destroy']),
            new Middleware('can:send-message,conversation', only: ['sendMessage']),
            new Middleware('can:join-conversation,conversation', only: ['joinConversation']),
        ];
    }

    public function __construct(protected Client $openai)
    {
    }

    public function index(Request $request)
    {
        $query = Conversation::query()
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

    public function store(StoreConversationRequest $request)
    {
        return Conversation::create($request->validated());
    }

    public function show(Conversation $conversation)
    {
        return $conversation;
    }

    public function update(UpdateConversationRequest $request, Conversation $conversation)
    {
        $conversation->update($request->validated());

        return $conversation;
    }

    public function destroy(Conversation $conversation)
    {
        $conversation->delete();

        return response()->noContent();
    }

    public function joinConversation(Request $request, Conversation $conversation)
    {
        $authUser = $request->user();

        $conversation->memberships()->create([
            'member_id' => $authUser->id,
            'member_type' => 'user',
        ]);

        return $conversation->load('users');
    }

    public function messages(Conversation $conversation)
    {
        $authUser = request()->user();

        $response = $this->openai->threads()->messages()->list($conversation->thread_id, [
            'order' => 'asc',
        ]);

        return $response->data;

        return Message::translateMany($response['data'], $authUser->locale);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $authUser = $request->user();

        $message = $this->openai->threads()->messages()->create($conversation->thread_id, [
            'content' => $request->content,
            'metadata' => [
                'receiver' => "conversation:{$conversation->getRouteKey()}",
                'sender' => "user:{$authUser->getRouteKey()}",
            ],
            'role' => 'user',
        ]);

        MessageSent::dispatch($message);

        return response()->noContent();
    }
}
