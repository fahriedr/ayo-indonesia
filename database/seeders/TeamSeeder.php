<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::factory()->count(5)->create();

        foreach ($teams as $team) {
            $jersey_numbers = range(1, 99);
            shuffle($jersey_numbers);

            foreach (range(1, 11) as $i) {
                Player::factory()->create([
                    'team_id' => $team->id,
                    'jersey_number' => array_pop($jersey_numbers),
                ]);
            }
        }
    }
}
