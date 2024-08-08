<?php

namespace Database\Seeders;

use App\Models\Bot;
use App\Models\Channel;
use App\Models\Membership;
use App\Models\User;
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
                fn ($channel) => Membership::create([
                    'member_id' => $user->id,
                    'member_type' => 'user',
                    'channel_id' => $channel->id,
                ])
            )
        );

        Bot::all()->each(
            fn ($bot) => Channel::all()->each(
                fn ($channel) => Membership::create([
                    'member_id' => $bot->id,
                    'member_type' => 'bot',
                    'channel_id' => $channel->id,
                ])
            )
        );
    }
}
