<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $clients = Client::all();

        return response()->json($clients, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $client = Client::find($id);

        if (empty($client)) {
            return response()->json(
                ['message' => 'Client not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($client, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $client = DB::transaction(function () use ($data) {
            $client = new Client();
            $client->fill($data);
            $client->save();

            return $client;
        });

        return new JsonResponse($client, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $client = Client::find($id);

        if (empty($client)) {
            return response()->json(
                ['message' => 'Client not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $client->name = $data['name'];
        }

        if (isset($data['phone'])) {
            $client->phone = $data['phone'];
        }

        if (isset($data['email'])) {
            $client->email = $data['email'];
        }

        $client->save();

        return new JsonResponse($client, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $client = Client::find($id);

        if (empty($client)) {
            return response()->json(
                ['message' => 'Client not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $client->delete();

        return new JsonResponse(['message' => 'Client deleted successfully'], Response::HTTP_OK);
    }
}
