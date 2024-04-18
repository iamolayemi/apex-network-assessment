<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

use function response;

trait ApiResponse
{
    private function apiResponse(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    public function respondSuccess(string $message): JsonResponse
    {
        return $this->apiResponse([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function respondSuccessWithData(?string $message = null, $contents = null): JsonResponse
    {
        $result['data'] = $contents;

        if ($contents instanceof LengthAwarePaginator) {
            $result['data'] = $contents->items();

            $result['meta'] = [
                'total' => $contents->total(),
                'per_page' => $contents->perPage(),
                'current_page' => $contents->currentPage(),
                'last_page' => $contents->lastPage(),
                'next_page_url' => $contents->nextPageUrl(),
                'prev_page_url' => $contents->previousPageUrl(),
                'from' => $contents->firstItem(),
                'to' => $contents->lastItem(),
            ];
        }

        if ($contents instanceof CursorPaginator) {
            $result['data'] = $contents->items();

            $result['meta'] = [
                'per_page' => $contents->perPage(),
                'next_cursor' => $contents->nextCursor()?->encode(),
                'prev_cursor' => $contents->previousCursor()?->encode(),
                'next_page_url' => $contents->nextPageUrl(),
                'prev_page_url' => $contents->previousPageUrl(),
            ];
        }

        return $this->apiResponse([
            'status' => 'success',
            'message' => $message,
            ...$result,
        ]);
    }

    public function respondForbidden(?string $message = null): JsonResponse
    {
        return $this->apiResponse([
            'status' => 'error',
            'message' => $message ?? 'Forbidden',
        ], Response::HTTP_FORBIDDEN);
    }

    public function respondError(?string $message = null): JsonResponse
    {
        return $this->apiResponse([
            'status' => 'error',
            'message' => $message ?? 'Error',
        ], Response::HTTP_BAD_REQUEST);
    }

    public function respondFailedValidation(string $message): JsonResponse
    {
        return $this->apiResponse([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function respondTooManyRequests(?string $message = null): JsonResponse
    {
        return $this->apiResponse([
            'status' => 'error',
            'message' => $message ?? 'Too Many Requests',
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }
}
