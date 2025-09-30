<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class PluginResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'slug'        => $this->slug,
            'name'        => $this->name,
            'version'     => $this->version,
            'enabled'     => $this->enabled,
            'installed'   => $this->installed,
            'path'        => $this->path,
            'repo_url'    => $this->repo_url,
            'author_name' => $this->author_name,
            'author_url'  => $this->author_url,
            'license'     => $this->license,
            'description' => $this->description,
            'requires'    => [
                'php'     => $this->requires_php,
                'laravel' => $this->requires_laravel,
                'mewpi'   => $this->requires_mewpi,
            ],
            'installed_at' => $this->installed_at,
            'enabled_at'   => $this->enabled_at,
            'disabled_at'  => $this->disabled_at,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
