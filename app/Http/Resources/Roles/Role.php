<?php

namespace App\Http\Resources\Roles;

use Illuminate\Http\Resources\Json\JsonResource;

class Role extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'alias'              => $this->alias,
            'role_type'          => $this->role_type,
            'description'        => $this->description,
            'root'               => $this->root,
            'permissions'        => $this->permissions,
            'security_clearance' => $this->security_clearance,
        ];
    }
}
