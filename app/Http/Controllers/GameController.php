<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function getAll(Request $request)
    {
        
    }

    public function get(Request $request, $id)
    {
        // Logic to retrieve a specific game by ID
    }

    public function create(Request $request)
    {
        $request->validate([
            'home_team_id' => 'required|integer',
            'away_team_id' => 'required|integer',
            'referee_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'status' => 'required|string|in:scheduled,live,completed,cancelled',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->only(['home_team_id', 'away_team_id', 'referee_id', 'date', 'time', 'status']);

            dd($data);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing game
    }

    public function delete(Request $request, $id)
    {
        // Logic to delete a game
    }
}
