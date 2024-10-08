<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Channel::all()->each(
            fn ($channel) => Message::factory()->count(10)->create([
                'receiver_id' => $channel->id,
                'receiver_type' => 'channel',
            ])
        );
    }
}
