<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class PluginsUpdateRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:150',
            'version'     => 'sometimes|string|max:50',
            'description' => 'nullable|string',
        ];
    }
}
