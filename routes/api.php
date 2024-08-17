<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\MessageController;
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
Route::get('/channels/{channel}/messages', [ChannelController::class, 'messages'])
    ->name('channels.messages.index');
Route::post('/channels/{channel}/messages', [ChannelController::class, 'sendMessage'])
    ->name('channels.messages.send')
    ->middleware('can:send-message,channel');
Route::get('/users/{user}/messages', [UserController::class, 'messages'])
    ->name('users.messages.index');
Route::post('/users/{user}/messages', [UserController::class, 'sendMessage'])
    ->name('users.messages.send')
    ->middleware('can:send-message,user');
Route::apiResource('bots', BotController::class);
Route::apiResource('channels', ChannelController::class);
Route::apiResource('users', UserController::class);
