<?php

namespace App\Http\Resources\SubscriptionPlanOption;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanOption extends JsonResource
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
        return parent::toArray($request);
    }
}
