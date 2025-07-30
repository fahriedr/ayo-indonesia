<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameScheduleResource extends JsonResource
{

    protected $team_id;
    protected $is_home;

    public function __construct($resource, $team_id = null)
    {
        parent::__construct($resource);
        $this->team_id = $team_id;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->is_home = $this->team_id == $this->home_team_id;

        $teamScore = $this->is_home ? $this->home_team_score : $this->away_team_score;
        $opponentScore = $this->is_home ? $this->away_team_score : $this->home_team_score;

        $opponentTeam = $this->is_home
            ? 
            [
                'id' => $this->away_team->id,
                'name' => $this->away_team->name,
                'logo' => $this->away_team->logo,
            ]
            : 
            [
                'id' => $this->home_team->id,
                'name' => $this->home_team->name,
                'logo' => $this->home_team->logo,
            ];

        $result = null;
        if ($this->status === 'completed') {
            if ($teamScore > $opponentScore) {
                $result = 'W';
            } elseif ($teamScore < $opponentScore) {
                $result = 'L';
            } else {
                $result = 'D';
            }
        }

        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
            'is_home_game' => $this->is_home,
            'opponent_team' => $opponentTeam,
            'score' => $this->status === 'completed'
                ? [
                    'team_score' => $teamScore,
                    'opponent_score' => $opponentScore,
                ]
                : null,
            'result' => $result, // null if not completed
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
