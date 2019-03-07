<?php

namespace App\Http\Resources\Projects;

use App\Http\Resources\Dialects\Dialect;
use Illuminate\Http\Resources\Json\JsonResource;

class Project extends JsonResource
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
            'title'            => $this->title,
            'description'      => $this->description,
            'dialects'         => Dialect::collection($this->dialects),
            'default_language' => new Dialect($this->dialects()->wherePivot('is_default', true)->first()),
        ];
    }
}
