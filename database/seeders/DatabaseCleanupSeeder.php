<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseCleanupSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@church.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('Super Admin');
    }
}
