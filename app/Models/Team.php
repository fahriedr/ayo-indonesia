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

    protected $appends = ['logo', 'total_wins', 'total_losses', 'total_draws', 'results'];

    public function getLogoAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function home_games()
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function away_games()
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    public function all_games()
    {
        return $this->hasMany(Game::class, 'home_team_id')
            ->union($this->hasMany(Game::class, 'away_team_id'));
    }

    public function getTotalWinsAttribute()
    {
        return Game::where(function ($query) {
            $query->where('home_team_id', $this->id)
                ->whereColumn('home_team_score', '>', 'away_team_score');
        })->orWhere(function ($query) {
            $query->where('away_team_id', $this->id)
                ->whereColumn('away_team_score', '>', 'home_team_score');
        })->count();
    }

    public function getTotalLossesAttribute()
    {
        return Game::where(function ($q) {
            $q->where('home_team_id', $this->id)
                ->whereColumn('home_team_score', '<', 'away_team_score');
        })->orWhere(function ($q) {
            $q->where('away_team_id', $this->id)
                ->whereColumn('away_team_score', '<', 'home_team_score');
        })->count();
    }

    public function getTotalDrawsAttribute()
    {
        return Game::where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('home_team_id', $this->id)
                    ->whereColumn('home_team_score', '=', 'away_team_score');
            })->orWhere(function ($sub) {
                $sub->where('away_team_id', $this->id)
                    ->whereColumn('away_team_score', '=', 'home_team_score');
            });
        })->count();
    }

    public function getResultsAttribute()
    {
        $games = Game::where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id)
            ->orderBy('date', 'desc')
            ->get();

        return $games->map(function ($game) {
            $is_home = $game->home_team_id === $this->id;
            $team_score = $is_home ? $game->home_team_score : $game->away_team_score;
            $opponent_score = $is_home ? $game->away_team_score : $game->home_team_score;

            return [
                'game_id' => $game->id,
                'date' => $game->date,
                'opponent_team_id' => $is_home ? $game->away_team_id : $game->home_team_id,
                'is_home' => $is_home,
                'team_score' => $team_score,
                'opponent_score' => $opponent_score,
                'result' => $team_score > $opponent_score ? 'W' : ($team_score < $opponent_score ? 'L' : 'D')
            ];
        });
    }
}
