<?php

namespace App\Http\Controllers;

use App\Models\Referee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefereeController extends Controller
{
    public function getAll(Request $request)
    {
        $keyword = $request->keyword ?? null;

        $referees = Referee::when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%');
        })
        ->orderBy('name', 'asc')
        ->paginate(10);

        return response()->json(['success' => true, 'data' => $referees], 200);
    }

    public function get(Request $request, $id)
    {
        $referee = Referee::find($id);

        if (!$referee) {
            return response()->json(['success' => false, 'message' => 'Referee not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $referee], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only(['name']);
            $referee = Referee::create($data);

            DB::commit();

            return response()->json(['success' => true, 'data' => $referee], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
        ]);

        $referee = Referee::find($id);

        if (!$referee) {
            return response()->json(['success' => false, 'message' => 'Referee not found'], 404);
        }

        DB::beginTransaction();

        try {
            $data = $request->only(['name']);
            $referee->update($data);

            DB::commit();

            return response()->json(['success' => true, 'data' => $referee], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $referee = Referee::find($id);

        if (!$referee) {
            return response()->json(['success' => false, 'message' => 'Referee not found'], 404);
        }

        DB::beginTransaction();

        try {
            $referee->soft_delete = now();
            $referee->save();
            
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Referee deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
