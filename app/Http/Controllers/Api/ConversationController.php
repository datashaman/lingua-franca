<?php

namespace App\Http\Controllers\Api;

use App\Enums\ConversationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use OpenAI\Client;
use OpenAI\Contracts\TArray;

class ConversationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(
                "auth:sanctum",
                only: ["store", "update", "destroy"]
            ),
            new Middleware(
                "can:view-any,App\Models\Conversation",
                only: ["index"]
            ),
            new Middleware("can:view,conversation", only: ["show"]),
            new Middleware(
                "can:create,App\Models\Conversation",
                only: ["store"]
            ),
            new Middleware("can:update,conversation", only: ["update"]),
            new Middleware("can:delete,conversation", only: ["destroy"]),
            new Middleware(
                "can:send-message,conversation",
                only: ["sendMessage"]
            ),
            new Middleware("can:join,conversation", only: ["join"]),
            new Middleware("can:leave,conversation", only: ["leave"]),
        ];
    }

    public function __construct(protected Client $openai)
    {
    }

    public function permissions(null|Conversation $conversation): array
    {
        $authUser = auth()->user();

        return [
            "create" => $authUser->can("create", Conversation::class),
            "join" => $conversation
                ? $authUser->can("join", $conversation)
                : false,
            "leave" => $conversation
                ? $authUser->can("leave", $conversation)
                : false,
            "view-any" => $authUser->can("view-any", Conversation::class),
        ];
    }

    public function index(Request $request): Collection
    {
        $joinedConversations = $request->user()->conversations()->get();

        $publicChannels = Conversation::query()
            ->where("type", ConversationType::PublicChannel)
            ->orderBy("is_system")
            ->orderBy("position")
            ->orderBy("name")
            ->get();

        return $publicChannels->merge($joinedConversations);
    }

    public function store(StoreConversationRequest $request): Conversation
    {
        return Conversation::query()->create($request->validated());
    }

    public function show(Conversation $conversation): Conversation
    {
        return $conversation;
    }

    public function update(
        UpdateConversationRequest $request,
        Conversation $conversation
    ): Conversation {
        $conversation->update($request->validated());

        return $conversation;
    }

    public function destroy(Conversation $conversation): void
    {
        $conversation->delete();
    }

    public function join(Request $request, Conversation $conversation): void
    {
        $request->user()->joinConversation($conversation);
    }

    public function leave(Request $request, Conversation $conversation): void
    {
        $request->user()->leaveConversation($conversation);
    }

    public function messages(
        Request $request,
        ConversationService $conversationService,
        Conversation $conversation
    ): Collection {
        return $conversationService->getMessages(
            $conversation,
            $request->user()
        );
    }

    public function sendMessage(
        Request $request,
        ConversationService $conversationService,
        Conversation $conversation
    ): array {
        $authUser = $request->user();

        return $conversationService
            ->sendMessage($conversation, $authUser, $request->content)
            ->toArray();
    }

    public function users(Conversation $conversation): Collection
    {
        return $conversation->users;
    }

    public function bots(Conversation $conversation): Collection
    {
        return $conversation->bots;
    }
}
