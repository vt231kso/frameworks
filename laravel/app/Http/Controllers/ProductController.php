<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    private array $products = [
        1 => ['id' => 1, 'title' => 'Ноутбук', 'price' => 25000],
        2 => ['id' => 2, 'title' => 'Мишка', 'price' => 500],
    ];

    public function getProducts(): JsonResponse
    {
        return response()->json(array_values($this->products), Response::HTTP_OK);
    }

    public function getProductItem(int $id): JsonResponse
    {
        if (!isset($this->products[$id])) {
            return response()->json(['error' => 'Товар не знайдено'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($this->products[$id], Response::HTTP_OK);
    }

    public function createProduct(Request $request): JsonResponse
    {

        $data = $request->all();

        $lastId = !empty($this->products) ? max(array_keys($this->products)) : 0;
        $newId = $lastId + 1;

        $newProduct = [
            'id' => $newId,
            'title' => $data['title'] ?? 'Новий товар',
            'price' => $data['price'] ?? 0,
        ];

        $this->products[$newId] = $newProduct;

        return response()->json([
            'message' => 'Товар додано в масив',
            'product' => $newProduct,
            'all_products' => array_values($this->products)
        ], Response::HTTP_CREATED);
    }


    public function updateProduct(int $id, Request $request): JsonResponse
    {
        if (!isset($this->products[$id])) {
            return response()->json(['error' => 'Товар не знайдено'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->all();

        if (isset($data['title'])) {
            $this->products[$id]['title'] = $data['title'];
        }
        if (isset($data['price'])) {
            $this->products[$id]['price'] = $data['price'];
        }

        return response()->json([
            'message' => "Товар з ID $id оновлено",
            'updated_product' => $this->products[$id]
        ], Response::HTTP_OK);
    }

    public function deleteProduct(int $id): JsonResponse
    {
        if (!isset($this->products[$id])) {
            return response()->json(['error' => 'Товар не знайдено'], Response::HTTP_NOT_FOUND);
        }

        unset($this->products[$id]);

        return response()->json([
            'message' => "Товар з ID $id видалено з масиву",
            'remaining_products' => array_values($this->products)
        ], Response::HTTP_OK);
    }
}
