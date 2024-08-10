<?php

namespace Database\Seeders;

use App\Models\Bot;
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
            'description' => '',
            'instructions' => '',
        ]);

        $this->call([
            ChannelSeeder::class,
        ]);

        $random = Channel::where('slug', 'random')->first();

        $user->joinChannel($random);
        $bot->joinChannel($random);
    }
}
