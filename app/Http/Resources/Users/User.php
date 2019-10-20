<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\ActiveSubscription\ActiveSubscription;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'name'                => $this->name,
            'email'               => $this->email,
            'stripe_id'           => $this->stripe_id,
            'activation_token'    => $this->activation_token,
            'last_login'          => $this->last_login,
            'activated_at'        => $this->activated_at,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'active_subscription' => new ActiveSubscription($this->activeSubscription),
        ];
    }
}
