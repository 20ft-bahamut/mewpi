<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ApiResponse
{
    public static function ok($data = [], string $message = 'success', int $status = 200)
    {
        // Eloquent 모델이면 배열로 변환
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        // Eloquent 컬렉션이면 배열로 변환
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }

        // null이면 빈 배열로 처리
        if ($data === null) {
            $data = [];
        }

        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error(
        string $code,
        string $message,
        int $status = 400,
        array $extra = []
    ) {
        return response()->json([
            'error' => [
                'code'    => $code,
                'message' => $message,
                ...$extra,
            ],
        ], $status);
    }
}
