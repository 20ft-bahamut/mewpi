<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class AdminsStoreRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:admin,email',
            'password' => 'required|string|min:8|max:255',
            'status'   => 'sometimes|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => '이름이 없습니다.',
            'email.required'    => '이메일이 없습니다.',
            'email.email'       => '이메일 형식이 올바르지 않습니다.',
            'email.unique'      => '이메일이 이미 존재합니다.',
            'password.required' => '비밀번호가 없습니다.',
            'status.in' => '상태값은 active 또는 inactive 여야 합니다.',
        ];
    }
}
