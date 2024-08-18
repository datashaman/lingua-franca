<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BotController extends Controller
{
    public function __invoke(Request $request, Bot $bot)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return Inertia::render('Guest/BotPage', [
                'bot' => $bot,
            ]);
        }

        $messages = Message::query()
            ->between($authUser, $bot)
            ->get();

        return Inertia::render('BotPage', [
            'bot' => $bot,
            'messages' => $messages,
        ]);
    }
}
