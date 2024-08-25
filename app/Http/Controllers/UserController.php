<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __invoke(
        Request $request,
        ConversationService $conversationService,
        User $user
    ) {
        $authUser = $request->user();

        $conversation = $conversationService->getDirectMessageConversation(
            $user,
            $authUser
        );

        return redirect()->route('conversations.show', $conversation);
    }
}
