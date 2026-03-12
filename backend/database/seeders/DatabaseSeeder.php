<?php

namespace Database\Seeders;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('user', 'web');

        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@travelorders.test',
            'password' => 'password',
        ]);

        $user = User::factory()->create([
            'name' => 'Common User',
            'email' => 'user@travelorders.test',
            'password' => 'password',
        ]);
        $user->assignRole('user');

        TravelOrder::factory()->count(4)->for($user)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        TravelOrder::factory()->count(2)->for($user)->create([
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        TravelOrder::factory()->count(2)->for($admin)->create();
    }
}
