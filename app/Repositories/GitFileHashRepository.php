<?php

namespace App\Repositories;

use App\Models\Translations\GitFileHash;
use App\Models\Translations\Project;
use Illuminate\Database\Connection;

class GitFileHashRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection      $dbConnection
     * @param \App\Models\Translations\GitFileHash $model
     */
    public function __construct(Connection $dbConnection, GitFileHash $model)
    {
        parent::__construct($dbConnection, $model);
    }

    public function findLastPullRequestByProject(Project $project)
    {
        $query = $this->getQuery();

        $query->whereHas('project', function ($query) use ($project) {
            $query->where('id', $project->id);
        })
            ->orderBy('created_at', 'desc')
        ;

        return $query->first();
    }

    public function findLastByProjectAndFile(Project $project, $file)
    {
        $query = $this->getQuery();

        $query->where('locale', $file['locale'])
            ->where('file', $file['file'])
            ->where('hash', $file['hash'])
            ->whereHas('project', function ($query) use ($project) {
                $query->where('id', $project->id);
            })
            ->orderBy('created_at', 'desc')
        ;

        return $query->first();
    }
}
