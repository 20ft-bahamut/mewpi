<?php

namespace App\Http\Requests;

use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        array_walk_recursive($data, function (&$v) {
            if (is_string($v)) $v = trim($v);
        });

        $this->replace($data);
    }

    protected function failedValidation(Validator $validator)
    {
        // ✅ 포지셔널 인자 사용: code, message, status, extra
        throw new HttpResponseException(
            ApiResponse::error(
                'VALIDATION_FAILED',
                '유효성 검사 실패',
                422,
                ['errors' => $validator->errors()]
            )
        );
    }

    protected function failedAuthorization()
    {
        // ✅ 포지셔널 인자 사용
        throw new HttpResponseException(
            ApiResponse::error(
                'FORBIDDEN',
                '이 요청을 수행할 권한이 없습니다.',
                403
            )
        );
    }
}
