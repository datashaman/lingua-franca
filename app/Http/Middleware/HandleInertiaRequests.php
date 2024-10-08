<?php

namespace App\Http\Middleware;

use App\Models\Bot;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use LaravelLang\NativeLocaleNames\LocaleNames;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $authUser = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $authUser,

                'permissions' => [
                    'bots' => [
                        'create' => $authUser?->can('create', Bot::class) ?? false,
                        'view-any' => $authUser?->can('view-any', Bot::class) ?? true,
                    ],
                    'channels' => [
                        'create' => $authUser?->can('create', Channel::class) ?? false,
                        'view-any' => $authUser?->can('view-any', Channel::class) ?? true,
                    ],
                    'users' => [
                        'create' => $authUser?->can('create', User::class) ?? false,
                        'view-any' => $authUser?->can('view-any', User::class) ?? true,
                    ],
                ],
            ],

            'locales' => collect(LocaleNames::get($authUser->locale ?? 'en'))
                ->filter(fn ($value, $key) => in_array($key, config('testing.locales'))),
        ];
    }
}
