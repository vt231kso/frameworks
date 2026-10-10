<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $orders = Order::all();

        return response()->json($orders, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::find($id);

        if (empty($order)) {
            return response()->json(
                ['message' => 'Order not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($order, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $order = DB::transaction(function () use ($data) {
            $order = new Order();
            $order->fill($data);
            $order->save();

            return $order;
        });

        return new JsonResponse($order, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $order = Order::find($id);

        if (empty($order)) {
            return response()->json(
                ['message' => 'Order not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['client_id'])) {
            $order->client_id = $data['client_id'];
        }

        if (array_key_exists('courier_id', $data)) {
            $order->courier_id = $data['courier_id'];
        }

        if (isset($data['product_id'])) {
            $order->product_id = $data['product_id'];
        }

        if (isset($data['delivery_address'])) {
            $order->delivery_address = $data['delivery_address'];
        }

        if (isset($data['total_amount'])) {
            $order->total_amount = $data['total_amount'];
        }

        if (isset($data['status'])) {
            $order->status = $data['status'];
        }

        $order->save();

        return new JsonResponse($order, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::find($id);

        if (empty($order)) {
            return response()->json(
                ['message' => 'Order not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $order->delete();

        return new JsonResponse(['message' => 'Order deleted successfully'], Response::HTTP_OK);
    }
}
