<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameScheduleResource;
use App\Models\Game;
use Illuminate\Http\Request;

class GameScheduleController extends Controller
{
    public function get(Request $request)
    {
        $team_id = $request->team_id ?? null;
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;

        $game = Game::where('home_team_id', $team_id)
            ->orWhere('away_team_id', $team_id)
            ->when($start_date, function ($query, $start_date) {
                return $query->whereDate('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                return $query->whereDate('date', '<=', $end_date);
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        $games = GameScheduleResource::collection($game)->through(function ($game) use ($team_id) {
            return new GameScheduleResource($game, $team_id);
        });

        return response()->json(['success' => true, 'data' => $games], 200);
    }
}
