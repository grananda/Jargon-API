<?php

namespace App\Http\Resources\SubscriptionPlan;

use App\Http\Resources\SubscriptionPlanOptionValue\SubscriptionPlanOptionValueCollection;
use App\Http\Resources\SubscriptionProduct\SubscriptionProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlan extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->uuid,
            'currency'   => $this->currency->code,
            'alias'      => $this->alias,
            'amount'     => $this->amount,
            'sort_order' => $this->sort_order,
            'interval'   => $this->interval,
            'is_active'  => (bool) $this->is_active,
            'options'    => new SubscriptionPlanOptionValueCollection($this->options),
            'product'    => new SubscriptionProduct($this->product),
        ];
    }
}
