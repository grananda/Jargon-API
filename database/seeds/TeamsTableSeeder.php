<?php

use App\Models\Team;
use App\Models\User;

class TeamsTableSeeder extends AbstractSeeder
{
    const TEAMS_LIMIT = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['teams']);

        $users = User::all();

        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            if ($user->hasRole('registered-user')) {
                $teamCount = (int) $user->activeSubscription
                    ->options()
                    ->where('option_key', 'team_count')
                    ->first()
                    ->option_value;

                if (is_null($teamCount) || $teamCount > 0) {
                    $teamCount = $teamCount > self::TEAMS_LIMIT ? self::TEAMS_LIMIT : $teamCount;

                    $teams = factory(Team::class, $teamCount)->create();

                    /** @var \App\Models\Team $team */
                    foreach ($teams as $team) {
                        $team->setOwner($user);
                    }
                }
            }
        }
    }
}
