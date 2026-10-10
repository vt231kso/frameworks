<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CourierController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $couriers = Courier::all();

        return response()->json($couriers, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $courier = Courier::find($id);

        if (empty($courier)) {
            return response()->json(
                ['message' => 'Courier not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($courier, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $courier = DB::transaction(function () use ($data) {
            $courier = new Courier();
            $courier->fill($data);
            $courier->save();

            return $courier;
        });

        return new JsonResponse($courier, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $courier = Courier::find($id);

        if (empty($courier)) {
            return response()->json(
                ['message' => 'Courier not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $courier->name = $data['name'];
        }

        if (isset($data['phone'])) {
            $courier->phone = $data['phone'];
        }

        if (array_key_exists('vehicle_type', $data)) {
            $courier->vehicle_type = $data['vehicle_type'];
        }

        $courier->save();

        return new JsonResponse($courier, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $courier = Courier::find($id);

        if (empty($courier)) {
            return response()->json(
                ['message' => 'Courier not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $courier->delete();

        return new JsonResponse(['message' => 'Courier deleted successfully'], Response::HTTP_OK);
    }
}
