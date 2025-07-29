<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name',
        'height',
        'weight',
        'position_id',
        'jersey_number',
        'team_id'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function position()
    {
        return $this->belongsTo(PlayerPosition::class, 'position_id');
    }
}
