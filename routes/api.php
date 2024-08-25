<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/me', fn (Request $request) => $request->user());

    Route::put('/users/translate', [UserController::class, 'translate'])->name(
        'users.translate'
    );

    Route::get('/bots/{bot}/messages', [
        BotController::class,
        'messages',
    ])->name('bots.messages.index');

    Route::get('/permissions/conversations/{conversation?}', [
        ConversationController::class,
        'permissions',
    ])->name('conversations.permissions');

    Route::post('/conversations/{conversation}/invite/{user}', [
        ConversationController::class,
        'invite',
    ])->name('conversations.invite');

    Route::post('/conversations/{conversation}/join', [
        ConversationController::class,
        'join',
    ])->name('conversations.join');

    Route::post('/conversations/{conversation}/leave', [
        ConversationController::class,
        'leave',
    ])->name('conversations.leave');

    Route::get('/conversations/{conversation}/messages', [
        ConversationController::class,
        'messages',
    ])->name('conversations.messages.index');

    Route::get('/conversations/{conversation}/bots', [
        ConversationController::class,
        'bots',
    ])->name('conversations.bots');

    Route::get('/conversations/{conversation}/users', [
        ConversationController::class,
        'users',
    ])->name('conversations.users');

    Route::post('/conversations/{conversation}/messages', [
        ConversationController::class,
        'sendMessage',
    ])
        ->name('conversations.messages.send')
        ->middleware('can:send-message,conversation');

    Route::apiResource('bots', BotController::class);
    Route::apiResource('conversations', ConversationController::class);
    Route::apiResource('users', UserController::class);
});
