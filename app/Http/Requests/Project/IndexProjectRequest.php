<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Request;
use App\Models\Translations\Project;

class IndexProjectRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', [Project::class]);
    }
}
