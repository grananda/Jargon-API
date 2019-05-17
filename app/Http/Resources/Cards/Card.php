<?php

namespace App\Http\Resources\Cards;

use Illuminate\Http\Resources\Json\JsonResource;

class Card extends JsonResource
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
            'id'              => $this->uuid,
            'stripe_id'       => $this->stripe_id,
            'brand'           => $this->brand,
            'last4'           => $this->last4,
            'exp_month'       => $this->exp_month,
            'exp_year'        => $this->exp_year,
            'country'         => $this->country,
            'address_city'    => $this->address_city,
            'address_country' => $this->address_country,
            'address_line1'   => $this->address_line1,
            'address_line2'   => $this->address_line2,
            'address_state'   => $this->address_state,
            'address_zip'     => $this->address_zip,
        ];
    }
}
