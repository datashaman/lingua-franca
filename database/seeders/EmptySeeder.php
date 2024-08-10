<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmptySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'marlinf@datashaman.com',
            'is_admin' => true,
            'handle' => 'datashaman',
            'locale' => 'en',
            'name' => 'Marlin Forbes',
            'password' => Hash::make('password'),
        ]);

        $bot = $user->bots()->create([
            'handle' => 'bot',
            'name' => 'Bot',
            'description' => 'An Afrikaans bot.',
            'instructions' => '',
            'locale' => 'af',
        ]);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'handle' => 'other',
            'locale' => 'de',
            'name' => 'Other User',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            ChannelSeeder::class,
        ]);

        $random = Channel::where('slug', 'random')->first();

        $user->joinChannel($random);
        $bot->joinChannel($random);
    }
}
