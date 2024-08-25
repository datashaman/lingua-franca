<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBotRequest;
use App\Http\Requests\UpdateBotRequest;
use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;

class BotController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware(
                "auth:sanctum",
                only: ["store", "update", "destroy"]
            ),
            new Middleware("can:view-any,App\Models\Bot", only: ["index"]),
            new Middleware("can:view,bot", only: ["show"]),
            new Middleware("can:create,App\Models\Bot", only: ["store"]),
            new Middleware("can:update,bot", only: ["update"]),
            new Middleware("can:delete,bot", only: ["destroy"]),
            new Middleware("can:send-message,bot", only: ["sendMessage"]),
        ];
    }

    public function index(Request $request): Collection
    {
        return Bot::query()->orderBy("name")->get();
    }

    public function show(Bot $bot): Bot
    {
        return $bot;
    }

    public function store(StoreBotRequest $request): Bot
    {
        return Bot::create($request->validated());
    }

    public function update(UpdateBotRequest $request, Bot $bot): Bot
    {
        $bot->update($request->validated());

        return $bot;
    }

    public function destroy(Bot $bot): void
    {
        $bot->delete();
    }
}
