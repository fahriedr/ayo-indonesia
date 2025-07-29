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
        Schema::table('games', function (Blueprint $table) {
            $table->integer('home_team_score')->default(0)->after('referee_id');
            $table->integer('away_team_score')->default(0)->after('home_team_score');
            $table->enum('status', ['scheduled', 'live', 'completed', 'canceled'])->default('scheduled')->after('away_team_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['home_team_score', 'away_team_score', 'status']);
        });
    }
};
