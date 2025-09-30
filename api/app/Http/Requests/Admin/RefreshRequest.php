<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class RefreshRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'refresh_token' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => '리프레시 토큰이 없습니다.',
        ];
    }
}
