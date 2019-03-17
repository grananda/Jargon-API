<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Request;
use App\Models\Team;

class IndexTeamRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', [Team::class]);
    }
}
