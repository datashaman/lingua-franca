<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\UserController;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::post('/channels/{channel}/messages', [ChannelController::class, 'sendMessage'])->name('channels.messages.send');

Route::apiResource('bots', BotController::class);
Route::apiResource('channels', ChannelController::class);
Route::apiResource('users', UserController::class);
