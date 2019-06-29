<?php

namespace App\Http\Resources\JargonOptions;

use Illuminate\Http\Resources\Json\JsonResource;

class JargonOptionsResource extends JsonResource
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
            'language'              => $this->language,
            'file_ext'              => $this->file_ext,
            'framework'             => $this->framework,
            'translation_file_mode' => $this->translation_file_mode,
            'project'               => $this->project->uuid,
        ];
    }
}
