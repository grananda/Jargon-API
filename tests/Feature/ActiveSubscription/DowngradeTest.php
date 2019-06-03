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
 * @covers \App\Http\Controllers\Subscription\ActiveSubscriptionDowngradeController::update
 */
class DowngradeTest extends TestCase
{
    use RefreshDatabase;
    use
        CreateActiveSubscription;

    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * @var array
     */
    private $stripeSubscriptionResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->user();

        factory(Card::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->stripeSubscriptionResponse = $this->loadFixture('stripe/subscription.update.success');

        $this->mock(StripeSubscriptionRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('swap')
                ->withArgs([$this->user, SubscriptionPlan::class])
                ->andReturn($this->stripeSubscriptionResponse)
            ;
        });
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->put(route('activeSubscriptions.downgrade.update'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_subscription_is_downgraded()
    {
        // Given
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($this->user, 'professional-month-eur');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', 'premium-month-eur')->first();

        // When
        $response = $this->signIn($this->user)->put(route('activeSubscriptions.downgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('active_subscriptions', [
            'subscription_plan_id' => $subscription->id,
            'user_id'              => $this->user->id,
            'stripe_id'            => $this->stripeSubscriptionResponse['id'],
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
    public function a_200_will_be_returned_when_a_subscription_is_downgraded_to_free()
    {
        // Given
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($this->user, 'professional-month-eur');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscription */
        $subscription = SubscriptionPlan::where('alias', SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN)->first();

        // When
        $response = $this->signIn($this->user)->put(route('activeSubscriptions.downgrade.update'), [
            'id' => $subscription->uuid,
        ]);

        // Then
        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('active_subscriptions', [
            'subscription_plan_id' => $subscription->id,
            'user_id'              => $this->user->id,
            'stripe_id'            => $this->stripeSubscriptionResponse['id'],
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
