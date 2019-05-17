<?php

namespace App\Http\Resources\SubscriptionPlanOptionValue;

use App\Http\Resources\SubscriptionOption\SubscriptionOption;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanOptionValue extends JsonResource
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
            'option_value' => $this->option_value,
            'option'       => new SubscriptionOption($this->key),
        ];
    }
}
