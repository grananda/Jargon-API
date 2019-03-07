<?php

namespace App\Http\Resources\Dialects;

use App\Http\Resources\Languages\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed locale
 * @property mixed country
 * @property mixed country_key
 * @property mixed language
 */
class Dialect extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'locale'     => $this->locale,
            'country'    => $this->country,
            'countryKey' => $this->country_key,
            'language'   => new Language($this->language),
        ];
    }
}
