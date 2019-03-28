<?php

namespace App\Http\Resources\Subscriptions;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlan extends JsonResource
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
            'amount'      => $this->amount,
            'status'      => (bool) $this->status,
        ];
    }
}
