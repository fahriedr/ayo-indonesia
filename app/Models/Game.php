<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'date',
        'time',
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'status',
        'referee_id',
        'stadium_id',
    ];

    public function home_team()
    {
        return $this->belongsTo(Team::class, "home_team_id");
    }

    public function away_team()
    {
        return $this->belongsTo(Team::class, "away_team_id");
    }

    public function referee()
    {
        return $this->belongsTo(Referee::class, "referee_id");
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, "game_id");
    }

    public function stadium()
    {
        return $this->belongsTo(Stadium::class, "stadium_id");
    }

}
