<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index()
    {
        return response()->json(Courier::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
        ]);

        $courier = Courier::create($validated);

        return response()->json($courier, 201);
    }

    public function show($id)
    {
        $courier = Courier::find($id);

        if (!$courier) {
            return response()->json(['message' => 'Courier not found'], 404);
        }

        return response()->json($courier, 200);
    }

    public function update(Request $request, $id)
    {
        $courier = Courier::find($id);

        if (!$courier) {
            return response()->json(['message' => 'Courier not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:50',
            'vehicle_type' => 'nullable|string|max:50',
        ]);

        $courier->update($validated);

        return response()->json($courier, 200);
    }

    public function destroy($id)
    {
        $courier = Courier::find($id);

        if (!$courier) {
            return response()->json(['message' => 'Courier not found'], 404);
        }

        $courier->delete();

        return response()->json(['message' => 'Courier deleted successfully'], 200);
    }
}
