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
     * The DialectRepository instance.
     *
     * @var \App\Repositories\DialectRepository
     */
    private $dialectRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection     $dbConnection
     * @param \App\Models\Translations\Project    $model
     * @param \App\Repositories\TeamRepository    $teamRepository
     * @param \App\Repositories\DialectRepository $dialectRepository
     */
    public function __construct(Connection $dbConnection, Project $model, TeamRepository $teamRepository, DialectRepository $dialectRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->teamRepository    = $teamRepository;
        $this->dialectRepository = $dialectRepository;
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

            $this->setProjectRelations($project, $attributes);

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

            $this->setProjectRelations($project, $attributes);

            return $project->fresh();
        });
    }

    /**
     * Set projects satellite relations.
     *
     * @param \App\Models\Translations\Project $project
     * @param array                            $attributes
     */
    private function setProjectRelations(Project $project, array $attributes)
    {
        if (isset($attributes['collaborators'])) {
            $this->addCollaborators($project, $attributes['collaborators']);
        }

        if (isset($attributes['teams'])) {
            $this->addTeams($project, $attributes['teams']);
        }

        if (isset($attributes['dialects'])) {
            $this->addDialects($project, $attributes['dialects']);
        }
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

    /**
     * Adds dialect to entity.
     *
     * @param \App\Models\Translations\Project $entity
     * @param array                            $dialects
     *
     * @return \App\Models\Translations\Project
     */
    private function addDialects(Project $entity, array $dialects)
    {
        $data = [];

        foreach ($dialects as $item) {
            $dialect = $this->dialectRepository->findBy([
                'locale' => $item['locale'],
            ]);

            $data[$dialect['id']] = ['is_default' => $item['default']];
        }

        $entity->setDialects($data);

        return $entity->refresh();
    }
}
