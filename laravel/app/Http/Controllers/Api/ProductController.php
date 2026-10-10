<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json(
                ['message' => 'Product not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = DB::transaction(function () use ($data) {
            $product = new Product();
            $product->fill($data);
            $product->save();

            return $product;
        });

        return new JsonResponse($product, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json(
                ['message' => 'Product not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['category_id'])) {
            $product->category_id = $data['category_id'];
        }

        if (isset($data['name'])) {
            $product->name = $data['name'];
        }

        if (isset($data['price'])) {
            $product->price = $data['price'];
        }

        $product->save();

        return new JsonResponse($product, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);

        if (empty($product)) {
            return response()->json(
                ['message' => 'Product not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $product->delete();

        return new JsonResponse(['message' => 'Product deleted successfully'], Response::HTTP_OK);
    }
}
