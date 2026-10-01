<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'product_id' => 'required|exists:products,id',
            'delivery_address' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        $order = Order::create($validated);

        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order, 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'courier_id' => 'nullable|exists:couriers,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'delivery_address' => 'sometimes|required|string|max:255',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        $order->update($validated);

        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
