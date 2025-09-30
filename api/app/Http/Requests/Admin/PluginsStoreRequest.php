<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class PluginsStoreRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'slug'        => 'required|string|max:100|unique:plugins,slug',
            'name'        => 'required|string|max:150',
            'version'     => 'required|string|max:50',
            'path'        => 'required|string|max:255',
            'manifest'    => 'required|array',
            'repo_url'    => 'nullable|string|max:255',
            'author_name' => 'nullable|string|max:150',
            'author_url'  => 'nullable|string|max:255',
            'license'     => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ];
    }
}
