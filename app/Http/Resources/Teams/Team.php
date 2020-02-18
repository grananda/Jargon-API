<?php

namespace App\Http\Resources\Teams;

use App\Http\Resources\Collaborators\CollaboratorCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class Team extends JsonResource
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
            'id'               => $this->uuid,
            'name'             => $this->name,
            'description'      => $this->description,
            'collaborators'    => new CollaboratorCollection($this->collaborators),
            'nonActiveMembers' => new CollaboratorCollection($this->nonActiveMembers),
        ];
    }
}
