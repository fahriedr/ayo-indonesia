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
            $table->boolean('is_penalty')->default(false)->after('assist_player_id');
            $table->unsignedInteger('minute')->after('is_penalty');
            $table->unsignedInteger('team_id')->after('minute');
            $table->boolean('is_own_goal')->default(false)->after('team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('is_penalty');
            $table->dropColumn('minute');
            $table->dropColumn('team_id');
            $table->dropColumn('is_own_goal');
        });
    }
};
