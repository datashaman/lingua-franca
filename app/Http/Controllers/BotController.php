<?php

namespace App\Http\Controllers;

use App\Enums\ConversationType;
use App\Models\Bot;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenAI\Client;

class BotController extends Controller
{
    public function __invoke(Request $request, Client $openai, Bot $bot)
    {
        $authUser = $request->user();

        if (! $authUser) {
            return Inertia::render('Guest/BotPage', [
                'bot' => $bot,
            ]);
        }

        $conversation = Conversation::between([$authUser, $bot], ConversationType::DirectMessage);

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => ConversationType::DirectMessage,
            ]);

            $conversation->memberships()->create([
                'member_type' => $authUser->getMorphClass(),
                'member_id' => $authUser->id,
            ]);

            $conversation->memberships()->create([
                'member_type' => $bot->getMorphClass(),
                'member_id' => $bot->id,
            ]);
        }

        $response = $openai->threads()->messages()->list($conversation->thread_id);

        return Inertia::render('BotPage', [
            'bot' => $bot,
            'messages' => $response['data'],
        ]);
    }
}
