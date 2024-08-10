<?php

namespace Database\Seeders;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Database\Seeder;

class BotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(
            fn ($user) => Bot::factory()->count(3)->for($user)->create()
        );
    }
}
