<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Game;
use App\Models\Goal;
use App\Models\Player;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    use Helpers;

    public function getAll(Request $request)
    {
        $game_id = $request->game_id ?? null;
        $player_id = $request->player_id ?? null;

        $goals = Goal::with(['game', 'player', 'assistPlayer'])
            ->when($game_id, function ($query, $game_id) {
                return $query->where('game_id', $game_id);
            })
            ->when($player_id, function ($query, $player_id) {
                return $query->where('player_id', $player_id);
            })
            ->orderBy('minute', 'asc')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $goals], 200);
    }

    public function get(Request $request, $id)
    {
        $goal = Goal::with(['game', 'player', 'assistPlayer'])
            ->where('id', $id)
            ->first();

        if (!$goal) {
            return response()->json(['success' => false, 'message' => 'Goal not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $goal], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'player_id' => 'required|exists:players,id',
            'assist_player_id' => 'nullable|exists:players,id',
            'is_penalty' => 'boolean',
            'minute' => 'required|integer|min:0|max:90',
            'team_id' => 'required|exists:teams,id',
            'is_own_goal' => 'boolean',
        ]);



        DB::beginTransaction();

        try {

            $game = Game::find($request->game_id);

            $team = Team::find($request->team_id);

            $player = Player::find($request->player_id);

            $validation = $this->validateGoal($request, $game, $team, $player);

            if (!$validation) {
                return response()->json(['success' => false, 'message' => 'Validation failed'], 400);
            }

            $goalData = $request->only([
                'game_id',
                'player_id',
                'assist_player_id',
                'is_penalty',
                'minute',
                'team_id',
                'is_own_goal',
            ]);

            $goal = Goal::create($goalData);

            if ($goal->team_id == $game->home_team_id) {
                $game->home_team_score += 1;
            } else {
                $game->away_team_score += 1;
            }

            $game->save();

            DB::commit();

            return response()->json(['success' => true, 'data' => $goal], 201);
        } catch (Exception $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'game_id' => 'sometimes|exists:games,id',
            'player_id' => 'sometimes|exists:players,id',
            'assist_player_id' => 'nullable|exists:players,id',
            'is_penalty' => 'boolean',
            'minute' => 'sometimes|integer|min:0|max:90',
            'team_id' => 'sometimes|exists:teams,id',
            'is_own_goal' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $goal = Goal::findOrFail($id);

            $game = Game::findOrFail($goal->game_id);

            // revert old goal effect
            if ($goal->team_id == $game->home_team_id) {
                $game->home_team_score -= 1;
            } else {
                $game->away_team_score -= 1;
            }

            // determine new team & player
            $newTeamId = $request->team_id ?? $goal->team_id;
            $newPlayerId = $request->player_id ?? $goal->player_id;

            $team = Team::findOrFail($newTeamId);
            $player = Player::findOrFail($newPlayerId);

            // validate with new values
            $this->validateGoal($request, $game, $team, $player);

            // update goal
            $goal->update($request->only([
                'player_id',
                'assist_player_id',
                'is_penalty',
                'minute',
                'team_id',
                'is_own_goal',
            ]));

            // apply new goal effect
            if ($goal->team_id == $game->home_team_id) {
                $game->home_team_score += 1;
            } else {
                $game->away_team_score += 1;
            }

            $game->save();

            DB::commit();
            return response()->json(['success' => true, 'data' => $goal], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        $goal = Goal::find($id);

        if (!$goal) {
            return response()->json(['success' => false, 'message' => 'Team not found'], 404);
        }

        DB::beginTransaction();

        try {

            $goal->deleted_at = now();
            $goal->save();

            $game = Game::find($goal->game_id);

            // revert goal effect
            if ($goal->team_id == $game->home_team_id) {
                $game->home_team_score -= 1;
            } else {
                $game->away_team_score -= 1;
            }

            $game->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Goal deleted successfully'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getTopScorers(Request $request)
    {
        $top_scorers = Goal::select('player_id', DB::raw('COUNT(*) as goals_count'))
            ->where('is_own_goal', 0)
            ->groupBy('player_id')
            ->orderBy('goals_count', 'desc')
            ->take(10)
            ->get();

        $top_scorers_data = $top_scorers->map(function ($scorer) {
            $player = Player::with(['team'])->find($scorer->player_id);
            return [
                'player_id' => $player->id,
                'player_name' => $player->name,
                'team_id' => $player->team_id,
                'goals_count' => $scorer->goals_count,
            ];
        });

        return response()->json(['success' => true, 'data' => $top_scorers_data], 200);
    }
}
