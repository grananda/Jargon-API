<?php


namespace Tests\Feature\ActiveSubscription;

use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use App\Repositories\Stripe\StripeCustomerRepository;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class UpgradeTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_403_will_be_returned_when_upgrading_from_a_higher_ranked_subscription()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', 'premium-month-eur')->first();

        // When
        $response = $this->signIn($user)->put(route('activeSubscriptions.upgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_subscription_is_upgraded_from_free()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['stripe_id' => null]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN, [], [
            'stripe_id' => null,
            'ends_at'   => null,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', 'professional-month-eur')->first();

        /** @var array $stripeSubscriptionResponse */
        $stripeSubscriptionResponse = $this->loadFixture('stripe/subscription.create.success');

        /** @var array $stripeCustomerResponse */
        $stripeCustomerResponse = $this->loadFixture('stripe/customer.create.success');

        $this->mock(StripeCustomerRepository::class, function ($mock) use ($user, $stripeCustomerResponse) {
            /** @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withArgs([$user])
                ->once()
                ->andReturn($stripeCustomerResponse);
        });

        $this->mock(StripeSubscriptionRepository::class, function ($mock) use ($user, $stripeSubscriptionResponse) {
            /** @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withArgs([$user, SubscriptionPlan::class])
                ->once()
                ->andReturn($stripeSubscriptionResponse);
        });

        // When
        $response = $this->signIn($user)->put(route('activeSubscriptions.upgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('active_subscriptions', [
            'subscription_plan_id' => $subscription->id,
            'user_id'              => $user->id,
            'stripe_id'            => $stripeSubscriptionResponse['id'],
        ]);

        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'stripe_id' => $stripeCustomerResponse['id'],
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