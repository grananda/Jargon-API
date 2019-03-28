<?php

namespace App\Http\Resources\SubscriptionPlanOption;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SubscriptionPlanOptionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
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
