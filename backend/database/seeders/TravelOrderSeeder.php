<?php

namespace Database\Seeders;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class TravelOrderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@admin.com')->first();
        $user = User::query()->where('email', 'user@user.com')->first();

        TravelOrder::factory()->count(4)->for($user)->create([
            'status' => TravelOrderStatusEnum::Requested->value,
        ]);

        TravelOrder::factory()->count(2)->for($user)->create([
            'status' => TravelOrderStatusEnum::Approved->value,
        ]);

        TravelOrder::factory()->count(2)->for($admin)->create();
    }
}
