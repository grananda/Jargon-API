<?php

namespace App\Http\Resources\Options;

use App\Http\Resources\OptionCaterories\OptionCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class Option extends JsonResource
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
            'title'           => $this->title,
            'description'     => $this->description,
            'option_type'     => $this->option_type,
            'is_private'      => (bool) $this->is_private,
            'option_category' => new OptionCategory($this->category),
            'option_scope'    => $this->option_scope,
            'option_value'    => $this->type === 'check' ? (bool) $this->option_value : (int) $this->option_value,
            'option_key'      => $this->option_key,
        ];
    }
}
