<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LaravelLang\NativeLocaleNames\LocaleNames;

class EmptySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = User::create([
            'email' => 'marlinf@datashaman.com',
            'is_admin' => true,
            'handle' => 'datashaman',
            'locale' => 'en',
            'name' => 'Marlin Forbes',
            'password' => Hash::make('password'),
            'translate' => true,
        ]);

        $this->call([
            ChannelSeeder::class,
        ]);

        $random = Channel::where('slug', 'random')->first();

        $locales = collect(LocaleNames::get('en'))
            ->filter(fn ($value, $key) => in_array($key, config('testing.locales')));

        foreach ($locales as $locale => $localeName) {
            $bot = $user->bots()->create([
                'handle' => "{$locale}-bot",
                'name' => "{$localeName} Bot",
                'description' => "A bot that speaks {$localeName}.",
                'instructions' => "Just talk to me in {$localeName}. Do not answer in any other language. I will not understand you.",
                'locale' => $locale,
            ]);
        }
    }
}
