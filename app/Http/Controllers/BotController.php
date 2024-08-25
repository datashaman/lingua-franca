<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function __invoke(
        Request $request,
        ConversationService $conversationService,
        Bot $bot
    ): RedirectResponse {
        $authUser = $request->user();

        $conversation = $conversationService->getDirectMessageConversation(
            $bot,
            $authUser
        );

        return redirect()->route('conversations.show', $conversation);
    }
}
