<?php


namespace Tests\Feature\ActiveSubscription;

use App\Models\Subscriptions\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class DowngradeTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_200_will_be_returned_when_a_subscription_is_downgraded()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN);

        // When
        $response = $this->signIn($user)->put(route('activeSubscriptions.upgrade.update'), [
            'uuid' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('active_subscriptions', [
            'subscription_plan_id' => $subscription->id,
            'user_id'              => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlanOptionValue $option */
        foreach ($subscription->options as $option) {
            $this->assertDatabaseHas('active_subscription_option_values', [
                'active_subscription_id' => $activeSubscription->id,
                'option_key'             => $option->option_key,
                'option_value'           => $option->option_value,
            ]);
        }
    }
}