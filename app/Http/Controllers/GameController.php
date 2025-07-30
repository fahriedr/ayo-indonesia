<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\Referee;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function getAll(Request $request)
    {

        $team_id = $request->team_id ?? null;
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;
        $status = $request->status ?? null;

        $games = Game::with(['home_team', 'away_team', 'referee'])
            ->when($team_id, function ($query, $team_id) {
                return $query->where(function ($q) use ($team_id) {
                    $q->where('home_team_id', $team_id)
                      ->orWhere('away_team_id', $team_id);
                });
            })
            ->when($start_date, function ($query, $start_date) {
                return $query->whereDate('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                return $query->whereDate('date', '<=', $end_date);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        $games = GameResource::collection($games);

        return response()->json(['success' => true, 'data' => $games], 200);
    }

    public function get(Request $request, $id)
    {
        $game = Game::with([
            'home_team' => function($query) {
                $query->select('id', 'name');
            },
            'away_team' => function($query) {
                $query->select('id', 'name');
            },
            'referee' => function($query) {
                $query->select('id', 'name');
            },
            'goals' => function($query) {
            $query->select('id', 'game_id', 'player_id', 'assist_player_id', 'minute', 'is_penalty', 'is_own_goal', 'team_id')
                ->with(['player' => function($q) {
                    $q->select('id', 'name');
                }, 'assistPlayer' => function($q) {
                    $q->select('id', 'name');
                }]);
            }
        ])
        ->find($id);

        if (!$game){
            return response()->json(['success' => false, 'message' => 'Game not found'], 404);
        }

        $game = new GameResource($game);

        return response()->json(['success' => true, 'data' => $game], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'home_team_id' => 'required|integer',
            'away_team_id' => 'required|integer',
            'referee_id' => 'required|integer',
            'status' => 'required|string|in:scheduled,live,completed,canceled',
        ]);

        $home_team = Team::find($request->home_team_id);

        if (!$home_team) {
            return response()->json(['success' => false, 'message' => 'Home Team not found'], 404);
        }

        $away_team = Team::find($request->away_team_id);

        if (!$away_team) {
            return response()->json(['success' => false, 'message' => 'Away Team not found'], 404);
        }

        $referee = Referee::find($request->referee_id);

        if (!$referee) {
            return response()->json(['success' => false, 'message' => 'Referee not found'], 404);
        }

        DB::beginTransaction();


        try {

            $game = Game::create([
                "date" => $request->date,
                "time" => $request->time,
                "home_team_id" => $request->home_team_id,
                "away_team_id" => $request->away_team_id,
                "referee_id" => $request->referee_id,
                "status" => $request->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Game created successfully',
                'data' => $game
            ], 200);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'date_format:Y-m-d',
            'time' => 'date_format:H:i',
            'home_team_id' => 'integer',
            'away_team_id' => 'integer',
            'referee_id' => 'integer',
            'status' => 'string|in:scheduled,live,completed,canceled',
        ]);

        $game = Game::find($id);

        if (!$game) {
            return response()->json(['success' => false, 'message' => 'Game not found'], 404);
        }

        if ($request->home_team_id){
            $home_team = Team::find($request->home_team_id);

            if (!$home_team) {
                return response()->json(['success' => false, 'message' => 'Home Team not found'], 404);
            }
        }

        if ($request->away_team_id){
            $away_team = Team::find($request->away_team_id);

            if (!$away_team) {
                return response()->json(['success' => false, 'message' => 'Away Team not found'], 404);
            }
        }

        DB::beginTransaction();


        try {

            $data = $request->only(['date', 'time', 'home_team_id', 'away_team_id', 'referee_id', 'status']);

            $game->update($data);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Game Updated successfully',
                'data' => $game
            ], 200);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $game = Game::find($id);

        if (!$game) {
            return response()->json(['success' => false, 'message' => 'Game not found'], 404);
        }

        DB::beginTransaction(); 

        try {

            $game->deleted_at = now();
            $game->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Game deleted successfully'], 200);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
