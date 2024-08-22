<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users/me', function (Request $request) {
        return $request->user();
    });
    Route::put('/users/translate', [UserController::class, 'translate'])->name('users.translate');
});

Route::get('/bots/{bot}/messages', [BotController::class, 'messages'])
    ->name('bots.messages.index');
Route::post('/bots/{bot}/messages', [BotController::class, 'sendMessage'])
    ->name('bots.messages.send')
    ->middleware('can:send-message,bot');
Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages'])
    ->name('conversations.messages.index');
Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])
    ->name('conversations.messages.send')
    ->middleware('can:send-message,conversation');
Route::get('/users/{user}/messages', [UserController::class, 'messages'])
    ->name('users.messages.index');
Route::post('/users/{user}/messages', [UserController::class, 'sendMessage'])
    ->name('users.messages.send')
    ->middleware('can:send-message,user');
Route::apiResource('bots', BotController::class);
Route::apiResource('conversations', ConversationController::class);
Route::apiResource('users', UserController::class);
