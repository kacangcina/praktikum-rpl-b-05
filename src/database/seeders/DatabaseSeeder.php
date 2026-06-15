<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => 'password',
                'role' => 'user',
                'is_verified' => false,
            ],
        );

        User::updateOrCreate(
            ['email' => 'admin@cubu.test'],
            [
                'name' => 'Admin CuBu',
                'username' => 'admin_cubu',
                'password' => 'password',
                'role' => 'admin',
                'is_verified' => false,
            ],
        );
    }
}
