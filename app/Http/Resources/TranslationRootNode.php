<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed id
 * @property mixed key
 * @property mixed pivot
 * @property mixed is_root
 * @property mixed item_token
 * @property mixed children
 */
class TranslationRootNode extends JsonResource
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
                'id'         => (int) $this->id,
                'key'        => $this->key,
                'index'      => (int) $this->pivot->sort_index,
                'is_root'    => (bool) $this->is_root,
                'project_id' => (int) $this->pivot->project_id,
                'parent_id'  => (int) $this->pivot->parent_id,
                'is_seed'    => $this->pivot->is_seed ? (bool) $this->pivot->is_seed : true,
                'token'      => $this->item_token,
                'children'   => TranslationNode::collection($this->children),
            ];
    }
}
