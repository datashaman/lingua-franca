<?php

namespace Database\Seeders;

use App\Models\Bot;
use App\Models\Channel;
use App\Models\Membership;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(
            fn ($user) => Channel::all()->each(
                fn ($channel) => Membership::factory()->create([
                    'user_id' => $user->id,
                    'channel_id' => $channel->id,
                ])
            )
        );

        Bot::all()->each(
            fn ($bot) => Channel::all()->each(
                fn ($channel) => Membership::factory()->create([
                    'user_id' => $bot->id,
                    'channel_id' => $channel->id,
                ])
            )
        );
    }
}
