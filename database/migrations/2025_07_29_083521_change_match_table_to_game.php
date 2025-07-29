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

        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['match_id']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->renameColumn('match_id', 'game_id');
        });

        Schema::table('matchs', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
        });

        Schema::rename('matchs', 'games');

        Schema::table('games', function (Blueprint $table) {
            $table->foreign('home_team_id')->references('id')->on('teams');
            $table->foreign('away_team_id')->references('id')->on('teams');
        });

        Schema::table('goals', function ($table) {
            $table->foreign('game_id')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->renameColumn('game_id', 'match_id');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
        });

        Schema::rename('games', 'matchs');

        Schema::table('matchs', function (Blueprint $table) {
            $table->foreign('home_team_id')->references('id')->on('teams');
            $table->foreign('away_team_id')->references('id')->on('teams');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->foreign('match_id')->references('id')->on('matchs');
        });
    }
};
