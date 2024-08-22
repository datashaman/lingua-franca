<?php

namespace Database\Seeders;

use App\Enums\ConversationType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position = 0;

        $user = User::where('handle', 'datashaman')->firstOrFail();

        $user->conversations()->create([
            'name' => 'Welcome Lounge',
            'slug' => 'welcome-lounge',
            'description' => 'A place for new members to introduce themselves and get to know the community.',
            'type' => ConversationType::PublicChannel,
            'is_system' => true,
            'position' => $position++,
        ]);

        $user->conversations()->create([
            'name' => 'Announcements',
            'slug' => 'announcements',
            'description' => 'Important announcements from the community.',
            'type' => ConversationType::PublicChannel,
            'is_system' => true,
            'position' => $position++,
        ]);

        $user->conversations()->create([
            'name' => 'Random',
            'slug' => 'random',
            'description' => 'A place for random discussions.',
            'type' => ConversationType::PublicChannel,
            'is_system' => true,
            'position' => $position++,
        ]);
    }
}
