<?php

namespace Tests\Unit\Models;

use App\Models\Organization;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Subscriptions\ActiveSubscriptionOptionValue;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @coversNothing
 */
class TeamModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_team_can_be_added_to_an_owner_user()
    {
        // Given
        /** @var SubscriptionPlan | null $subscriptionPlan */
        $subscriptionPlan = SubscriptionPlan::findByAliasOrFail('professional-month-eur');

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user');

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = factory(ActiveSubscription::class)->create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $subscriptionPlan->id,
            'subscription_active'  => true,
        ]);

        foreach ($subscriptionPlan->options as $option) {
            factory(ActiveSubscriptionOptionValue::class)->create([
                'active_subscription_id' => $activeSubscription->id,
                'option_key'             => $option->option_key,
                'option_value'           => $option->option_value,
            ]);
        }

        /** @var \App\Models\Organization $organization */
        $organization = factory(Organization::class)->create();
        $organization->setOwner($user);

        /** @var \App\Models\Team $team */
        $team = factory(Team::class)->create();

        // When
        $team->setOwner($user);

        // Then
        $this->assertSame($user->uuid, $team->owners()->first()->uuid);
    }
}
