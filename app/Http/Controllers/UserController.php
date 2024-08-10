<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        return Inertia::render('UserPage', [
            'user' => $user,
            'messages' => $request->user()
                ? Message::query()
                    ->between($request->user(), $user)
                    ->oldest()
                    ->get()
                : [],
        ]);
    }
}
