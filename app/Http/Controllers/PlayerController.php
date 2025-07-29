<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    use \App\Helpers\Helpers;


    public function getAll(Request $request)
    {
        $keyword = $request->keyword ?? null;
        $team_id = $request->team_id ?? null;
        $position_id = $request->position_id ?? null;

        $players = Player::with([
            'team' => function ($query) {
                $query->select('id', 'name as team_name');
            }, 
            'position' => function ($query) {
                $query->select('id', 'name as position_name');
            }])
            ->when($keyword, function ($query, $keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('team', function ($q) use ($keyword) {
                                $q->where('name', 'like', '%' . $keyword . '%');
                            });
            })
            ->when($team_id, function ($query, $team_id) {
                return $query->where('team_id', $team_id);
            })
            ->when($position_id, function ($query, $position_id) {
                return $query->where('position_id', $position_id);
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $players], 200);
    }

    public function get(Request $request, $id)
    {
        $player = Player::with('team', 'position')
            ->find($id);

        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Player not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $player], 200);
    }

    public function create(Request $request) {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'height' => 'required|integer',
                'weight' => 'required|integer',
                'position_id' => 'required|integer',
                'jersey_number' => 'required|integer',
                'team_id' => 'required|integer',
            ]);

            $team = Team::find($request->team_id);

            if (!$team) {
                return response()->json(['success' => false, 'message' => 'Team not found'], 404);
            }

            $player = Player::where('team_id', $request->team_id)
                ->where('jersey_number', $request->jersey_number)
                ->first();

            if ($player) {
                return response()->json(['success' => false, 'message' => 'Jersey number already exists for this team'], 400);
            }

            $player = Player::create([
                'name' => $request->name,
                'height' => $request->height,
                'weight' => $request->weight,
                'position_id' => $request->position_id,
                'jersey_number' => $request->jersey_number,
                'team_id' => $request->team_id,
            ]);
            

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Player created successfully',
                'data' => $player
            ], 200);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function update(Request $request, $id) {

        $request->validate([
            'name' => 'string|max:255',
            'height' => 'integer',
            'weight' => 'integer',
            'position_id' => 'integer',
            'jersey_number' => 'integer',
            'team_id' => 'integer',
        ]);

        $player = Player::find($id);

        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Player not found'], 404);
        }

        if($request->team_id) {
            $team = Team::find($request->team_id);

            if (!$team) {
                return response()->json(['success' => false, 'message' => 'Team not found'], 404);
            }

           if ($request->jersey_number) {
                $existingPlayer = Player::where('team_id', $request->team_id)
                    ->where('jersey_number', $request->jersey_number)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingPlayer) {
                    return response()->json(['success' => false, 'message' => 'Jersey number already exists for this team'], 400);
                }
            }
        }

        DB::beginTransaction();

        try {
            $data = $request->only(['name', 'height', 'weight', 'position_id', 'jersey_number', 'team_id']);

            $player->update($data);

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Player updated successfully',
                'data' => $player
            ], 200);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }


    }

    public function delete($id) {
        $team = Player::find($id);

        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Team not found'], 404);
        }

        DB::beginTransaction(); 

        try {

            $team->deleted_at = now();
            $team->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Team deleted successfully'], 200);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
