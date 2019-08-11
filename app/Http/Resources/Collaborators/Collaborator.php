<?php

namespace App\Http\Resources\Collaborators;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource;

class Collaborator extends JsonResource
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
            'id'       => $this->uuid,
            'name'     => $this->name,
            'email'    => $this->email,
            'is_owner' => (bool)$this->pivot->is_owner,
            'is_valid' => (bool)$this->pivot->is_valid,
            'role'     => new \App\Http\Resources\Roles\Role(Role::find($this->pivot->role_id)),
        ];
    }
}
