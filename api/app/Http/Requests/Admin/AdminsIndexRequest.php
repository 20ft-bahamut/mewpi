<?php
namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class AdminsIndexRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'per_page'     => 'nullable|integer|min:1|max:100',
            'page'         => 'nullable|integer|min:1',
            'search_field' => 'nullable|string|in:id,name,email,status',
            'search_term'  => 'nullable|string|max:150',
            'sort_by'      => 'nullable|string|in:id,name,email,status,last_login_at,created_at',
            'direction'    => 'nullable|string|in:asc,desc',
        ];
    }
}
