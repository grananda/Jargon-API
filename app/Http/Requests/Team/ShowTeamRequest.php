<?php

namespace App\Http\Requests\Team;

use App\Http\Requests\Request;
use App\Models\Team;

class ShowTeamRequest extends Request
{
    /**
     * The Team instance.
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
        $teamId = (string) $this->route('id');

        $this->team = Team::findByUuidOrFail($teamId);

        return $this->user()->can('show', [Team::class, $this->team]);
    }
}
