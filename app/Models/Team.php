<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'year_founded',
        'address',
        'city',
    ];

    public function getLogoAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }


}
