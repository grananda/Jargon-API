<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Request;
use App\Models\Team;

class DeleteTeamRequest extends Request
{
    /**
     * The team instance.
     *
     * @var \App\Models\Team
     */
    public $team;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->team = Team::findByUuidOrFail($this->route('id'));

        return $this->user()->can('delete', $this->team);
    }
}
