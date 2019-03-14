<?php

namespace App\Repositories;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Traits\InvitationTrait;
use Illuminate\Database\Connection;

class TeamRepository extends CoreRepository
{
    use InvitationTrait;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Team                $model
     */
    public function __construct(Connection $dbConnection, Team $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * Creates a new team for a given user owner.
     *
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createTeam(User $user, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $attributes) {
            /** @var \App\Models\Team $team */
            $team = $this->createWithOwner($user, $attributes);

            $this->addCollaborators($team, $attributes['collaborators']);

            return $team->fresh();
        });
    }

    /**
     * Updates an existing team.
     *
     * @param \App\Models\Team $team
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateOrganization(Team $team, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($team, $attributes) {
            /** @var \App\Models\Team $team */
            $team = $this->update($team, $attributes);

            $this->addCollaborators($team, $attributes['collaborators']);

            return $team->fresh();
        });
    }
}
