<?php

namespace App\Http\Resources\SubscriptionOption;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionOption extends JsonResource
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
            'option_key'           => $this->option_key,
            'title'                => $this->title,
            'description'          => $this->description,
            'description_template' => $this->description_template,
        ];
    }
}
