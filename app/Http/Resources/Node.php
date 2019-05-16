<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Node extends JsonResource
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
        return
            [
                'id'         => $this->uuid,
                'key'        => $this->key,
                'route'      => $this->route,
                'is_root'    => ! (bool) $this->parent_id,
                'sort_index' => $this->sort_index ? (int) $this->sort_index : null,
                'project_id' => $this->project_id ? (int) $this->project_id : null,
                'parent_id'  => $this->parent_id ? (int) $this->parent_id : null,
                'is_seed'    => true,
                'token'      => $this->uuid,
                'children'   => self::collection($this->children),
            ];
    }
}
