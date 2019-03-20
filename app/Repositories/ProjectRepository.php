<?php

namespace App\Repositories;

use App\Models\Translations\Project;
use App\Models\User;
use Illuminate\Database\Connection;

class ProjectRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection  $dbConnection
     * @param \App\Models\Translations\Project $model
     */
    public function __construct(Connection $dbConnection, Project $model)
    {
        parent::__construct($dbConnection, $model);
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
}
