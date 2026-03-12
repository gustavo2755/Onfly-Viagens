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
    private const REQUESTERS = [
        'João Silva', 'Maria Santos', 'Ana Paula Oliveira', 'Carlos Eduardo Souza',
        'Fernanda Costa', 'Roberto Almeida', 'Juliana Mendes', 'Pedro Henrique Lima',
        'Camila Rodrigues', 'Lucas Ferreira', 'Amanda Pereira', 'Rafael Barbosa',
        'Patricia Nascimento', 'Bruno Carvalho', 'Larissa Martins',
    ];

    private const DESTINATIONS = [
        'São Paulo', 'Rio de Janeiro', 'Lisboa', 'Barcelona', 'Paris', 'Nova York',
        'Salvador', 'Florianópolis', 'Curitiba', 'Belo Horizonte', 'Porto Alegre',
        'Madrid', 'Londres', 'Buenos Aires', 'Santiago',
    ];

    public function definition(): array
    {
        $departure = now()->addDays(fake()->numberBetween(1, 30));
        $return = $departure->copy()->addDays(fake()->numberBetween(3, 14));

        return [
            'user_id' => User::factory(),
            'requester_name' => fake()->randomElement(self::REQUESTERS),
            'destination' => fake()->randomElement(self::DESTINATIONS),
            'departure_date' => $departure->toDateString(),
            'return_date' => $return->toDateString(),
            'status' => fake()->randomElement([
                TravelOrderStatusEnum::Requested->value,
                TravelOrderStatusEnum::Approved->value,
                TravelOrderStatusEnum::Cancelled->value,
            ]),
        ];
    }
}
