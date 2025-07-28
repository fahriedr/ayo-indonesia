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
        Schema::create('goals', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("match_id");
            $table->unsignedInteger("player_id");
            $table->unsignedInteger("assist_player_id");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('goals', function ($table) {
            $table->foreign('match_id')->references('id')->on('matchs');
            $table->foreign('player_id')->references('id')->on('players');
            $table->foreign('assist_player_id')->references('id')->on('players');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
