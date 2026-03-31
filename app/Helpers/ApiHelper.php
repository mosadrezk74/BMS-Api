<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if (! function_exists('api_response')) {

    function api_response(
        bool $success,
        int $status_code = ResponseAlias::HTTP_OK,
        ?string $message = null,
        mixed $data = null,
        mixed $errors = null,
        bool $paginate = false
    ): JsonResponse {

        // Normalize and validate HTTP status code. Symfony throws if invalid (e.g., 0).
        $status_code = (int) $status_code;
        if ($status_code < 100 || $status_code > 599) {
            $status_code = $success ? ResponseAlias::HTTP_OK : ResponseAlias::HTTP_BAD_REQUEST;
        }

        $response = [
            'success' => $success,
            'status_code' => $status_code,
            'message' => $message,
        ];

        if (! is_null($data)) {
            $response['data'] = $data;
        }

        if (! is_null($errors)) {
            $response['errors'] = $errors;
        }

        // Secure pagination
        if ($paginate && $data instanceof LengthAwarePaginator) {
            $response['pagination'] = [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'links' => [
                    'first' => $data->url(1),
                    'last' => $data->url($data->lastPage()),
                    'prev' => $data->previousPageUrl(),
                    'next' => $data->nextPageUrl(),
                ],
            ];
        }

        return response()->json($response, $status_code);
    }
}

if (! function_exists('api_success')) {

    function api_success(
        mixed $data = null,
        ?string $message = 'Success',
        int $status_code = ResponseAlias::HTTP_OK,
        bool $paginate = false
    ): JsonResponse {
        return api_response(true, $status_code, $message, $data, null, $paginate);
    }
}

if (! function_exists('api_error')) {

    function api_error(
        ?string $message = 'An error occurred',
        int $status_code = ResponseAlias::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        return api_response(false, $status_code, $message, null, $errors);
    }
}

if (! function_exists('api_result')) {

    function api_result(array $result): JsonResponse
    {
        $success = (bool) ($result['success'] ?? false);

        return $success
            ? api_success(
                $result['data'] ?? null,
                $result['message'] ?? 'Success',
                (int) ($result['status'] ?? ResponseAlias::HTTP_OK),
                (bool) ($result['paginate'] ?? false)
            )
            : api_error(
                $result['message'] ?? 'Error',
                (int) ($result['status'] ?? ResponseAlias::HTTP_BAD_REQUEST),
                $result['errors'] ?? null
            );
    }
}
