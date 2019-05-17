<?php

namespace App\Http\Resources\ActiveSubscription;

use App\Http\Resources\SubscriptionPlan\SubscriptionPlan;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveSubscription extends JsonResource
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
            'id'                  => $this->uuid,
            'stripe_id'           => $this->stripe_id,
            'ends_at'             => $this->ends_at,
            'subscription_active' => $this->subscription_active,
            'plan'                => new SubscriptionPlan($this->subscriptionPlan),
            'user'                => $this->user,
        ];
    }
}
