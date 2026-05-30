<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function error(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function deleted(string $message = 'Deleted successfully'): JsonResponse
    {
        return $this->success(null, $message, 200);
    }

    protected function paginated(ResourceCollection $collection, string $message = 'Success'): JsonResponse
    {
        $resource = $collection->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource['data'] ?? [],
            'meta' => [
                'current_page' => $resource['meta']['current_page'] ?? null,
                'from' => $resource['meta']['from'] ?? null,
                'last_page' => $resource['meta']['last_page'] ?? null,
                'per_page' => $resource['meta']['per_page'] ?? null,
                'to' => $resource['meta']['to'] ?? null,
                'total' => $resource['meta']['total'] ?? null,
            ],
            'links' => $resource['links'] ?? null,
        ]);
    }
}
