<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'marlinf@datashaman.com',
            'is_admin' => true,
            'handle' => 'datashaman',
            'locale' => 'en',
            'name' => 'Marlin Forbes',
            'password' => Hash::make('password'),
        ]);

        User::factory()->count(10)->create();

        $this->call([
            BotSeeder::class,
            ConversationSeeder::class,
            MembershipSeeder::class,
        ]);
    }
}
