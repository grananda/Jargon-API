<?php

namespace App\Http\Resources;

use App\Http\Resources\Dialects\Dialect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Translation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->uuid,
            'definition' => $this->definition,
            'dialect'    => new Dialect($this->dialect),
        ];
    }
}
