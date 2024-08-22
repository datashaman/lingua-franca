<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenAI\Client;

class ConversationController extends Controller
{
    public function __invoke(Request $request, Client $openai, Conversation $conversation)
    {
        // $openai->threads()->delete($conversation->thread_id);
        // $conversation->thread_id = $openai->threads()->create([])->id;
        // $conversation->save();

        $response = $openai->threads()->messages()->list($conversation->thread_id, [
            'order' => 'asc',
        ]);

        dump($response->data);

        return Inertia::render('ConversationPage', [
            'conversation' => $conversation,
            'members' => $conversation->members,
            'messages' => $response->data,
        ]);
    }
}
