<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\BotService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use LaravelLang\NativeLocaleNames\LocaleNames;

class BotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(BotService $botService): void
    {
        $systemUser = User::where('email', 'system@example.com')->first();

        $locales = collect(LocaleNames::get('en'))->filter(
            fn ($value, $key) => in_array($key, config('testing.locales'))
        );

        foreach ($locales as $locale => $language) {
            $attributes = collect(config('bot'))->map(
                fn ($value) => Str::replace(['{{language}}', '{{locale}}'], [$language, $locale], $value)
            );

            $botService->createBot(
                $systemUser,
                name: $attributes->get('name'),
                handle: $attributes->get('handle'),
                instructions: $attributes->get('instructions'),
                description: $attributes->get('description'),
                locale: $locale
            );
        }
    }
}
