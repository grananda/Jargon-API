<?php

namespace App\Http\Resources\SubscriptionProduct;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionProduct extends JsonResource
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
            'id'          => $this->uuid,
            'title'       => $this->title,
            'description' => $this->description,
            'alias'       => $this->alias,
            'rank'        => $this->rank,
            'is_active'   => (bool) $this->is_active,
        ];
    }
}
