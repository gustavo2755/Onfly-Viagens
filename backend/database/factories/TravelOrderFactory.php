<?php

namespace Database\Factories;

use App\Enums\TravelOrderStatusEnum;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TravelOrder>
 */
class TravelOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'requester_name' => fake()->name(),
            'destination' => fake()->city(),
            'departure_date' => now()->addDays(fake()->numberBetween(1, 30))->toDateString(),
            'return_date' => now()->addDays(fake()->numberBetween(31, 60))->toDateString(),
            'status' => fake()->randomElement([
                TravelOrderStatusEnum::Requested->value,
                TravelOrderStatusEnum::Approved->value,
                TravelOrderStatusEnum::Cancelled->value,
            ]),
        ];
    }
}
