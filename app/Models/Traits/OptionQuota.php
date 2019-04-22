<?php

namespace App\Models\Traits;

trait OptionQuota
{
    /**
     * Returns ActiveSubscription allowed organizations.
     *
     * @return mixed
     */
    public function getAllowedOrganizations()
    {
        return $this->options()
            ->where('option_key', 'organization_count')
            ->first()
            ->option_value;
    }

    /**
     * Returns ActiveSubscription allowed projects.
     *
     * @return int
     */
    public function getAllowedProjects()
    {
        return $this->options()
            ->where('option_key', 'project_count')
            ->first()
            ->option_value;
    }

    /**
     * Returns ActiveSubscription allowed teams.
     *
     * @return int
     */
    public function getAllowedTeams()
    {
        return $this->options()
            ->where('option_key', 'team_count')
            ->first()
            ->option_value;
    }

    /**
     * Returns ActiveSubscription allowed collaborators.
     *
     * @return int
     */
    public function getAllowedCollaborators()
    {
        return $this->options()
            ->where('option_key', 'collaborator_count')
            ->first()
            ->option_value;
    }
}
