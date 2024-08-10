<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position = 0;

        $user = User::where('handle', 'datashaman')->firstOrFail();

        $user->ownedChannels()->create([
            'name' => 'Welcome Lounge',
            'slug' => 'welcome-lounge',
            'description' => 'A place for new members to introduce themselves and get to know the community.',
            'is_public' => true,
            'is_system' => true,
            'position' => $position++,
        ]);

        $user->ownedChannels()->create([
            'name' => 'Announcements',
            'slug' => 'announcements',
            'description' => 'Important announcements from the community.',
            'is_public' => true,
            'is_system' => true,
            'position' => $position++,
        ]);

        $user->ownedChannels()->create([
            'name' => 'Random',
            'slug' => 'random',
            'description' => 'A place for random discussions.',
            'is_public' => true,
            'is_system' => true,
            'position' => $position++,
        ]);
    }
}
