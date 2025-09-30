<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class LoginRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string',
            // (선택) 'force' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => '이메일이 없습니다.',
            'email.email'       => '이메일 형식이 올바르지 않습니다.',
            'password.required' => '비밀번호가 없습니다.',
        ];
    }
}
