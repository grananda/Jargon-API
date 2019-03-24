<?php

namespace App\Policies;

use App\Models\Translations\Project;
use App\Models\User;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;
use App\Repositories\ProjectRepository;

class ProjectPolicy extends AbstractPolicy
{
    use ActiveSubscriptionRestrictionsTrait;

    /** @var \App\Repositories\ProjectRepository */
    private $projectRepository;

    /**
     * ProjectPolicy constructor.
     *
     * @param \App\Repositories\ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Determines is a user can list projects.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * @param User                             $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function show(User $user, Project $project)
    {
        return (bool) $this->projectRepository->findAllByMember($user)->map(function ($item) use ($project) {
            return $item->id === $project->id;
        })->count();
    }

    /**
     * @param User $user
     * @param int  $collaboratorsSize
     *
     * @return bool
     */
    public function create(User $user, int $collaboratorsSize)
    {
        return $this->hasActiveSubscription($user)
            && (bool) $this->getCurrentSubscriptionProjectQuota($user)
            && $this->getCurrentSubscriptionCollaborationQuota($user) >= $collaboratorsSize;
    }

    /**
     * @param User                             $user
     * @param \App\Models\Translations\Project $project
     * @param int                              $collaboratorsSize
     *
     * @return bool
     */
    public function update(User $user, Project $project, int $collaboratorsSize)
    {
        $currentSubscriptionCollaborationQuota = $this->getCurrentSubscriptionCollaborationQuota($user) + $project->members()->count();

        return $this->hasActiveSubscription($user)
            && (bool) $project->isOwner($user)
            && $currentSubscriptionCollaborationQuota >= $collaboratorsSize;
    }

    /**
     * @param User                             $user
     * @param \App\Models\Translations\Project $project
     *
     * @return bool
     */
    public function delete(User $user, Project $project)
    {
        return $project->isOwner($user);
    }
}
