<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param Builder $query
     * @param Request $request
     * @param array $allowedFields
     * @param array|null $likeFields
     * @return JsonResponse
     */
    protected function filterAndPaginate(
        Builder $query,
        Request $request,
        array $allowedFields,
        ?array $likeFields = null
    ): JsonResponse {
        if ($likeFields === null) {
            $modelClass = get_class($query->getModel());
            $likeFields = defined("{$modelClass}::LIKE_FIELDS") ? $modelClass::LIKE_FIELDS : [];
        }


        $allQueryParams = array_keys($request->query->all());
        $systemParams = ['page', 'itemsPerPage'];

        foreach ($allQueryParams as $queryParam) {
            if (in_array($queryParam, $systemParams, true)) {
                continue;
            }


            $cleanParam = preg_replace('/_(from|to)$/', '', $queryParam);

            if (!in_array($queryParam, $allowedFields, true) && !in_array($cleanParam, $allowedFields, true)) {
                throw new \InvalidArgumentException("Invalid query parameter: '{$queryParam}'");
            }
        }


        foreach ($allowedFields as $field) {
            if (in_array($field, ['page', 'itemsPerPage'], true)) {
                continue;
            }

            $val = $request->query($field);
            $fromVal = $request->query($field . '_from');
            $toVal = $request->query($field . '_to');

            if ($val !== null && $val !== '') {
                if (in_array($field, $likeFields, true)) {
                    $query->where($field, 'like', '%' . $val . '%');
                } else {
                    $query->where($field, $val);
                }
            }

            if ($fromVal !== null && $fromVal !== '') {
                $query->where($field, '>=', $fromVal);
            }


            if ($toVal !== null && $toVal !== '') {
                $query->where($field, '<=', $toVal);
            }
        }

        $itemsPerPage = max(1, min($request->query->getInt('itemsPerPage', 10), 100));
        $page = max(1, $request->query->getInt('page', 1));

        $items = $query->forPage($page, $itemsPerPage)->get();

        if ($items->isEmpty()) {
            return response()->json(
                ['message' => 'No records found matching your criteria'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($items, Response::HTTP_OK);
    }
}
