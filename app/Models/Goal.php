<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'game_id',
        'player_id',
        'assist_player_id',
        'is_penalty',
        'minute',
        'team_id',
        'is_own_goal'
    ];

    protected $casts = [
        'is_penalty' => 'boolean',
        'is_own_goal' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function assistPlayer()
    {
        return $this->belongsTo(Player::class, 'assist_player_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

}
