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
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
        ];
    }
}
