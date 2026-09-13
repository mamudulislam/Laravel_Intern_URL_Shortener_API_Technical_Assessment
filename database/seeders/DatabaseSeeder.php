<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@tinylink.test'],
            ['name' => 'Demo User', 'password' => Hash::make('password')]
        );

        $user->urls()->firstOrCreate(
            ['short_code' => 'example'],
            ['original_url' => 'https://laravel.com']
        );
    }
}
