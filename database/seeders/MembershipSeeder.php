<?php

namespace Database\Seeders;

use App\Models\Bot;
use App\Models\Conversation;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(
            fn ($user) => Conversation::all()->each(
                fn ($conversation) => Membership::create([
                    'member_id' => $user->id,
                    'member_type' => 'user',
                    'conversation_id' => $conversation->id,
                ])
            )
        );

        Bot::all()->each(
            fn ($bot) => Conversation::all()->each(
                fn ($conversation) => Membership::create([
                    'member_id' => $bot->id,
                    'member_type' => 'bot',
                    'conversation_id' => $conversation->id,
                ])
            )
        );
    }
}
