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
        return Inertia::render('BotPage', [
            'bot' => $bot,
            'messages' => $request->user()
                ? Message::query()->between($request->user(), $bot)->get()
                : [],
        ]);
    }
}
