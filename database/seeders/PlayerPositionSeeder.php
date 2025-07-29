<?php

namespace Database\Seeders;

use App\Models\PlayerPosition;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = ['Goalkeeper', 'Defender', 'Midfielder', 'Forward'];

        foreach ($positions as $position) {
            PlayerPosition::firstOrCreate(['name' => $position]);
        }
    }
}
