<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class AdminsUpdateStatusRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => '상태값이 없습니다.',
            'status.in'       => '상태값은 active 또는 inactive 여야 합니다.',
        ];
    }
}
