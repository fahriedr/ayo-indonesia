<?php

namespace Database\Factories;

use App\Models\PlayerPosition;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            "name" => fake()->name(),
            "height" => mt_rand(170, 190),
            "weight" => mt_rand(65, 80),
            "position_id" => PlayerPosition::inRandomOrder()->first()->id,
            "jersey_number" => fake()->numberBetween(1, 99),
        ];
    }

    public function forTeam(Team $team)
    {
        static $jersey_numbers = [];

        return $this->state(function (array $attributes) use ($team, &$jersey_numbers) {
            if (!isset($jersey_numbers[$team->id])) {
                $jersey_numbers[$team->id] = range(1, 99);
                shuffle($jersey_numbers[$team->id]);
            }

            return [
                'team_id' => $team->id,
                'jersey_number' => array_pop($jersey_numbers[$team->id]),
            ];
        });
    }
}
