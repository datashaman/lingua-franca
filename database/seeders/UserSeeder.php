<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'email' => 'system@example.com',
            'is_admin' => true,
            'handle' => 'system',
            'locale' => 'en',
            'name' => 'System User',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'email' => 'marlinf@datashaman.com',
            'is_admin' => true,
            'handle' => 'datashaman',
            'locale' => 'en',
            'name' => 'Marlin Forbes',
            'password' => Hash::make('password'),
            'translate' => true,
        ]);
    }
}
