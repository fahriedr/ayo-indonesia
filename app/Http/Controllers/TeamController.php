<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    use \App\Helpers\Helpers;

    public function getAll(Request $request)
    {

        $keyword = $request->keyword ?? null;

        $teams = Team::when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%')
                         ->orWhere('city', 'like', '%' . $keyword . '%');
        })
        ->orderBy('name', 'asc')
        ->paginate(10);

        return response()->json(['success' => true, 'data' => $teams], 200);
    }

    public function get(Request $request, $id)
    {
        $team = Team::with(['players', 'all_games'])
            ->find($id);

        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Team not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $team], 200);
    }

    public function create(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required',
            'year_founded' => 'integer',
            'address' => 'string|max:255',
            'city' => 'string|max:255',
        ]);


        DB::beginTransaction();

        try {
           

            $imagePath = $this->uploadFile($request->logo);

            $team = Team::create([
                'name' => $request->name,
                'logo' => $imagePath,
                'year_founded' => $request->year_founded,
                'address' => $request->address,
                'city' => $request->city,
            ]);
            

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Team created successfully',
                'data' => $team
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
        
        $team = Team::find($id);

        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Team not found'], 404);
        }

        DB::beginTransaction();

        $request->validate([
            'name' => 'string|max:255',
            'logo' => 'nullable',
            'year_founded' => 'integer',
            'address' => 'string|max:255',
            'city' => 'string|max:255',
        ]);

        try {
            $data = $request->only(['name', 'year_founded', 'address', 'city']);

            if ($request->has('logo')) {
                $imagePath = $this->uploadFile($request->logo);
                $data['logo'] = $imagePath;
            }

            $team->update($data);

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Team updated successfully',
                'data' => $team
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
        $team = Team::find($id);

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
