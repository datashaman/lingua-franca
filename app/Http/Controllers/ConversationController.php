<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function __invoke(
        Request $request,
        ConversationService $conversationService,
        Conversation $conversation
    ) {
        $authUser = $request->user();
        $messages = $conversationService->getMessages($conversation, $authUser);

        $conversation->load(["bots", "users"]);

        return Inertia::render("ConversationPage", [
            "conversation" => $conversation,
            "messages" => $messages,
            "isMember" => $request->user()->isMember($conversation),
        ]);
    }
}
