<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Format a successful API response.
     *
     * @param mixed $data
     * @return JsonResponse
     */
    protected function successResponse($data, $message = 'success' ): JsonResponse
    {

        return response()->json([
            'status' => '200',
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Format an error API response.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse($message, $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => (string)$statusCode,
            'message' => $message,
            'data' => []
        ], $statusCode);
    }
}