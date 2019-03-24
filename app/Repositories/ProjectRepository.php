<?php

namespace App\Repositories;

use App\Models\Translations\Project;
use App\Models\User;
use App\Repositories\Traits\InvitationTrait;
use Illuminate\Database\Connection;

class ProjectRepository extends CoreRepository
{
    use InvitationTrait;

    /**
     * The TeamRepository instance.
     *
     * @var \App\Repositories\TeamRepository
     */
    private $teamRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection  $dbConnection
     * @param \App\Models\Translations\Project $model
     * @param \App\Repositories\TeamRepository $teamRepository
     */
    public function __construct(Connection $dbConnection, Project $model, TeamRepository $teamRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->teamRepository = $teamRepository;
    }

    /**
     * Gets all items where user is member.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByMember(User $user)
    {
        return $this->getQuery()
            ->whereHas('collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.user_id', $user->id);
                $query->where('collaborators.is_valid', true);
            })
            ->orWhereHas('teams.collaborators', function ($query) use ($user) {
                /* @var \Illuminate\Database\Query\Builder $query */
                $query->where('collaborators.user_id', $user->id);
                $query->where('collaborators.is_valid', true);
            })
            ->orderByDesc('id')
            ->get()
        ;
    }

    /**
     * Creates a new project for a given user owner.
     *
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createProject(User $user, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($user, $attributes) {
            /** @var \App\Models\Translations\Project $project */
            $project = $this->createWithOwner($user, $attributes);

            $this->addCollaborators($project, $attributes['collaborators']);

            $this->addTeams($project, $attributes['teams']);

            return $project->fresh();
        });
    }

    /**
     * Updates an existing team.
     *
     * @param \App\Models\Translations\Project $project
     * @param array                            $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateTeam(Project $project, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($project, $attributes) {
            /** @var Project $project */
            $project = $this->update($project, $attributes);

            $this->addCollaborators($project, $attributes['collaborators']);

            $this->addTeams($project, $attributes['teams']);

            return $project->fresh();
        });
    }

    /**
     * Adds team to entity.
     *
     * @param \App\Models\Translations\Project $entity
     * @param array                            $teams
     *
     * @return \App\Models\Translations\Project
     */
    private function addTeams(Project $entity, array $teams)
    {
        $teams = $this->teamRepository->findAllWhereIn([
            'uuid' => $teams,
        ]);

        $entity->setTeams($teams->pluck('id')->toArray());

        return $entity->refresh();
    }
}
