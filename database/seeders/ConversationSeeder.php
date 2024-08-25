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

        $user = User::where('handle', 'system')->firstOrFail();

        $user->ownedConversations()->create([
            'name' => 'Welcome Lounge',
            'description' => 'A place for new members to introduce themselves and get to know the community.',
            'type' => ConversationType::PublicChannel,
            'is_system' => true,
            'position' => $position++,
        ]);

        $user->ownedConversations()->create([
            'name' => 'Announcements',
            'description' => 'Important announcements from the community.',
            'type' => ConversationType::PublicChannel,
            'is_system' => true,
            'position' => $position++,
        ]);

        $user->ownedConversations()->create([
            'name' => 'Random',
            'description' => 'A place for random discussions.',
            'type' => ConversationType::PublicChannel,
            'is_system' => true,
            'position' => $position++,
        ]);
    }
}
