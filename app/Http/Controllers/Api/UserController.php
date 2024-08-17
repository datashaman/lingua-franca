<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('can:view-any,App\Models\User', only: ['index']),
            new Middleware('can:view,user', only: ['show']),
            new Middleware('can:create,App\Models\User', only: ['store']),
            new Middleware('can:update,user', only: ['update']),
            new Middleware('can:delete,user', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($authUser = $request->user()) {
            return User::orderBy('handle')
                ->get();
        } else {
            return User::where('is_public', true)
                ->orderBy('handle')
                ->get();
        }
    }

    public function store(StoreUserRequest $request)
    {
        return User::create($request->validated());
    }

    public function show(User $user)
    {
        return $user;
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }

    public function translate(Request $request)
    {
        $authUser = $request->user();
        $authUser->translate = $request->boolean('translate');
        $authUser->save();

        return response()->noContent();
    }
}
