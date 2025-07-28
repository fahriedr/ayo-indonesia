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
        Schema::create('players', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->isNotEmpty();
            $table->integer("height")->isNotEmpty();
            $table->integer("weight")->isNotEmpty();
            $table->unsignedInteger("position_id");
            $table->integer("jersey_number")->isNotEmpty();
            $table->unsignedInteger("team_id")->isNotEmpty();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('players', function ($table) {
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('position_id')->references('id')->on('player_positions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
