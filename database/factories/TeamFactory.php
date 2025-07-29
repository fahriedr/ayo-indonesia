<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->city();
        return [
            'name' => $city . " FC",
            'logo' => fake()->imageUrl(),
            'year_founded' => mt_rand(1900, 2000),
            'address' => fake()->address(),
            'city' => $city 
        ];
    }
}
