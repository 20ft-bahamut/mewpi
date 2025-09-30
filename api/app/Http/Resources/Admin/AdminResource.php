<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'status'        => $this->status,
            'last_login_at' => $this->last_login_at,
            'created_at'    => $this->created_at,
        ];
    }
}
