<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
            ]
        );
        $admin->syncRoles(['admin']);

        $user = User::query()->updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Common User',
                'password' => 'password',
            ]
        );
        $user->syncRoles(['user']);
    }
}
