<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBotRequest;
use App\Http\Requests\UpdateBotRequest;
use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BotController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('can:view-any,App\Models\Bot', only: ['index']),
            new Middleware('can:view,bot', only: ['show']),
            new Middleware('can:create,App\Models\Bot', only: ['store']),
            new Middleware('can:update,bot', only: ['update']),
            new Middleware('can:delete,bot', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($authUser = $request->user()) {
            return Bot::orderBy('name')
                ->get();
        }

        return Bot::where('is_public', true)
            ->orderBy('name')
            ->get();
    }

    public function show(Bot $bot)
    {
        return $bot;
    }

    public function store(StoreBotRequest $request)
    {
        return Bot::create($request->validated());
    }

    public function update(UpdateBotRequest $request, Bot $bot)
    {
        $bot->update($request->validated());

        return $bot;
    }

    public function destroy(Bot $bot)
    {
        $bot->delete();

        return response()->noContent();
    }
}
