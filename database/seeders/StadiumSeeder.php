<?php

namespace Database\Seeders;

use App\Models\Stadium;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stadium::factory()->count(5)->create();
    }
}
