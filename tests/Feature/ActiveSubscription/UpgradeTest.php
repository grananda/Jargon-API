<?php

namespace Tests\Feature\ActiveSubscription;

use App\Models\Card;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group feature
 * @coversNothing
 */
class UpgradeTest extends TestCase
{
    use RefreshDatabase;
    use
        CreateActiveSubscription;

    /**
     * @var array
     */
    private $stripeSubscriptionCreateResponse;

    /**
     * @var array
     */
    private $stripeSubscriptionUpdateResponse;

    /**
     * @var \App\Models\User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->user();

        factory(Card::class)->create([
            'user_id' => $this->user->id,
        ]);

        /* @var array $stripeSubscriptionCreateResponse */
        $this->stripeSubscriptionCreateResponse = $this->loadFixture('stripe/subscription.create.success');

        /* @var array $stripeSubscriptionResponse */
        $this->stripeSubscriptionUpdateResponse = $this->loadFixture('stripe/subscription.update.success');

        $this->mock(StripeSubscriptionRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withArgs([$this->user, SubscriptionPlan::class])
                ->andReturn($this->stripeSubscriptionCreateResponse)
            ;

            $mock->shouldReceive('swap')
                ->withArgs([$this->user, SubscriptionPlan::class])
                ->andReturn($this->stripeSubscriptionUpdateResponse)
            ;
        });
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('activeSubscriptions.upgrade.update'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_upgrading_from_a_higher_ranked_subscription()
    {
        // Given
        $this->createActiveSubscription($this->user, 'professional-month-eur');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', 'premium-month-eur')->first();

        // When
        $response = $this->signIn($this->user)->put(route('activeSubscriptions.upgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_subscription_is_upgraded_from_free()
    {
        // Given
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($this->user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN, [], [
            'stripe_id' => null,
            'ends_at'   => null,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', 'professional-month-eur')->first();

        // When
        $response = $this->signIn($this->user)->put(route('activeSubscriptions.upgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('active_subscriptions', [
            'subscription_plan_id' => $subscription->id,
            'user_id'              => $this->user->id,
            'stripe_id'            => $this->stripeSubscriptionCreateResponse['id'],
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

    /** @test */
    public function a_200_will_be_returned_when_a_subscription_is_upgraded_non_free()
    {
        // Given
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($this->user, 'premium-month-eur');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', 'professional-month-eur')->first();

        // When
        $response = $this->signIn($this->user)->put(route('activeSubscriptions.upgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('active_subscriptions', [
            'subscription_plan_id' => $subscription->id,
            'user_id'              => $this->user->id,
            'stripe_id'            => $this->stripeSubscriptionUpdateResponse['id'],
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
