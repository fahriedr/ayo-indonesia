<?php

namespace App\Http\Controllers;

use App\Models\PlayerPosition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerPositionController extends Controller
{

    public function getAll(Request $request)
    {

        $data = PlayerPosition::orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            
            $data = PlayerPosition::create([
                'name' => $request->name,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'data' => $data], 200);

        } catch (Exception $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }

        return response()->json(['success' => true, 'data' => []], 200);
    }

    public function update(Request $request, $id)
    {
        $data = PlayerPosition::find($id);

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Player Position not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $data->update([
                'name' => $request->name,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'data' => $data], 200);

        } catch (Exception $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $data = PlayerPosition::find($id);
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Player Position not found'], 404);
        }

        DB::beginTransaction();

        try {
            $data->deleted_at = now();
            $data->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Player Position deleted successfully'], 200);
        } catch (Exception $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
