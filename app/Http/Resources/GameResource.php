<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'home_team' => [
                'id' => $this->home_team->id,
                'name' => $this->home_team->name,
                'score' => $this->home_team_score,
            ],
            'away_team' => [
                'id' => $this->away_team->id,
                'name' => $this->away_team->name,
                'score' => $this->away_team_score,
            ],
            'referee' => [
                'id' => $this->referee->id,
                'name' => $this->referee->name,
            ],
            'goals' => $this->goals->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'player' => [
                        'id' => $goal->player->id,
                        'name' => $goal->player->name,
                    ],
                    'assist_player' => $goal->assistPlayer ? [
                        'id' => $goal->assistPlayer->id,
                        'name' => $goal->assistPlayer->name,
                    ] : null,
                    'team_id' => $goal->team_id,
                    'minute' => $goal->minute,
                    'is_penalty' => $goal->is_penalty,
                    'is_own_goal' => $goal->is_own_goal,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
        ];
    }
}
