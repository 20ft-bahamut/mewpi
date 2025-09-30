<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class AdminsResetPasswordRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:8|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => '비밀번호가 없습니다.',
        ];
    }
}
