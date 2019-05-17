<?php

use App\Models\Dialect;
use App\Models\Translations\Project;
use App\Models\User;

class ProjectsTableSeeder extends AbstractSeeder
{
    const PROJECTS_LIMIT = 5;

    public function run()
    {
        $this->truncateTables(['projects', 'dialect_project', 'project_team']);

        $users = User::all();

        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            if ($user->hasRole('registered-user')) {
                $projectCount = (int) $user->activeSubscription
                    ->options()
                    ->where('option_key', 'project_count')
                    ->first()
                    ->option_value;

                if (is_null($projectCount) || $projectCount > 0) {
                    $projectCount = $projectCount > self::PROJECTS_LIMIT ? self::PROJECTS_LIMIT : $projectCount;

                    $projects = factory(Project::class, $projectCount)->create();

                    /** @var Project $project */
                    foreach ($projects as $project) {
                        $project->setOwner($user);

                        /** @var \App\Models\Dialect $dialect */
                        $dialect = Dialect::inRandomOrder()->first();
                        $project->setDialects([$dialect->id => ['is_default' => true]]);

                        /** @var \App\Models\Organization $organization */
                        if ($organization = $user->organizations()->inRandomOrder()->first()) {
                            $project->setOrganization($organization);

                            /** @var \App\Models\Team $team */
                            if ($team = $user->teams()->where('is_owner', true)->inRandomOrder()->first()) {
                                $project->setTeams([$team->id]);
                            }
                        }
                    }
                }
            }
        }
    }
}
