<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed item_token
 * @property mixed id
 * @property mixed key
 * @property mixed translations
 */
class TranslationKey extends JsonResource
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
            'id'           => $this->id,
            'key'          => $this->key,
            'token'        => $this->item_token,
            'translations' => Translation::collection($this->translations),
        ];
    }
}
