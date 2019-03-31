<?php

namespace App\Http\Resources\OptionCaterories;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OptionCategoryCollection extends ResourceCollection
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
