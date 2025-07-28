<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matchs', function (Blueprint $table) {
            $table->increments("id");
            $table->date("date");
            $table->time("time");
            $table->unsignedInteger("home_team_id");
            $table->unsignedInteger("away_team_id");
            $table->unsignedInteger("referee_id");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('matchs', function ($table) {
            $table->foreign('home_team_id')->references('id')->on('teams');
            $table->foreign('away_team_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchs');
    }
};
