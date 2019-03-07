<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed key
 * @property mixed is_root
 * @property mixed item_token
 * @property mixed pivot
 * @property mixed children
 */
class TranslationNode extends JsonResource
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
        return
            [
                'id'        => (int) $this->id,
                'key'       => $this->key,
                'index'     => (int) $this->pivot->sort_index,
                'is_root'   => (bool) $this->is_root,
                'parent_id' => (int) $this->pivot->parent_id,
                'is_seed'   => (bool) $this->pivot->is_seed,
                'token'     => $this->item_token,
                'children'  => self::collection($this->children),
            ];
    }
}
