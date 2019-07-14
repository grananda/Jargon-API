<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OrganizationSeeder extends AbstractSeeder
{
    const ORGANIZATIONS_LIMIT = 2;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['organizations']);

        Mail::fake();

        $users = User::all();

        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            if ($user->hasRole('registered-user')) {
                $organizationCount = (int) $user->activeSubscription
                    ->options()
                    ->where('option_key', 'organization_count')
                    ->first()
                    ->option_value;

                if (is_null($organizationCount) || $organizationCount > 0) {
                    $organizationCount = $organizationCount > self::ORGANIZATIONS_LIMIT ? self::ORGANIZATIONS_LIMIT : $organizationCount;

                    $organizations = factory(Organization::class, $organizationCount)->create();

                    /** @var \App\Models\Organization $organization */
                    foreach ($organizations as $organization) {
                        $organization->setOwner($user);
                    }
                }
            }
        }
    }
}
