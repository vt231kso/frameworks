<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return response()->json($categories, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (empty($category)) {
            return response()->json(
                ['message' => 'Category not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($category, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = DB::transaction(function () use ($data) {
            $category = new Category();
            $category->fill($data);
            $category->save();

            return $category;
        });

        return new JsonResponse($category, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (empty($category)) {
            return response()->json(
                ['message' => 'Category not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $category->name = $data['name'];
        }

        if (array_key_exists('description', $data)) {
            $category->description = $data['description'];
        }

        $category->save();

        return new JsonResponse($category, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (empty($category)) {
            return response()->json(
                ['message' => 'Category not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $category->delete();

        return new JsonResponse(['message' => 'Category deleted successfully'], Response::HTTP_OK);
    }
}
