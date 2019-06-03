<?php

namespace Tests\Unit\Services;

use App\Events\ActiveSubscription\ActiveSubscriptionWasActivated;
use App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated;
use App\Exceptions\ActiveSubscriptionStatusException;
use App\Exceptions\StripeMissingCardException;
use App\Exceptions\StripeMissingCustomerException;
use App\Models\Card;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group unit
 * @coversNothing
 */
class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreateActiveSubscription;

    /**
     * @var array
     */
    private $stripeSubscriptionCreateResponse;

    /**
     * @var array
     */
    private $stripeSubscriptionUpdateResponse;

    /**
     * @var array
     */
    private $stripeSubscriptionCancelResponse;

    /**
     * @var array
     */
    private $stripeSubscriptionReactivateResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->stripeSubscriptionCreateResponse = $this->loadFixture('stripe/subscription.create.success');

        $this->stripeSubscriptionUpdateResponse = $this->loadFixture('stripe/subscription.update.success');

        $this->stripeSubscriptionCancelResponse = $this->loadFixture('stripe/subscription.cancel.success');

        $this->stripeSubscriptionReactivateResponse = $this->loadFixture('stripe/subscription.reactivate.success');

        $this->mock(StripeSubscriptionRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withAnyArgs()
                ->andReturn($this->stripeSubscriptionCreateResponse)
            ;

            $mock->shouldReceive('swap')
                ->withAnyArgs()
                ->andReturn($this->stripeSubscriptionUpdateResponse)
            ;

            $mock->shouldReceive('cancel')
                ->withAnyArgs()
                ->andReturn($this->stripeSubscriptionCancelResponse)
            ;

            $mock->shouldReceive('reactivate')
                ->withAnyArgs()
                ->andReturn($this->stripeSubscriptionReactivateResponse)
            ;
        });
    }

    /** @test */
    public function a_non_stripe_customer_does_not_subscribes_to_a_subscription()
    {
        // Given
        $this->expectException(StripeMissingCustomerException::class);

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['stripe_id' => null]);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        $subscriptionService->subscribe($user, $subscriptionPlan);
    }

    /** @test */
    public function a_customer_does_not_subscribes_to_a_subscription_without_cc()
    {
        // Given
        $this->expectException(StripeMissingCardException::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        $subscriptionService->subscribe($user, $subscriptionPlan);
    }

    /** @test */
    public function a_customer_subscribes_to_a_subscription()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->subscribe($user, $subscriptionPlan);

        // Then
        $this->assertSame($activeSubscription->subscriptionPlan->alias, $subscriptionPlan->alias);
        $this->assertSame($activeSubscription->stripe_id, $this->stripeSubscriptionCreateResponse['id']);
    }

    /** @test */
    public function a_customer_subscribes_to_a_subscription_from_free()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN, [], [
            'stripe_id' => null,
            'ends_at'   => null,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->subscribe($user, $subscriptionPlan);

        // Then
        $this->assertSame($activeSubscription->subscriptionPlan->alias, $subscriptionPlan->alias);
        $this->assertSame($activeSubscription->stripe_id, $this->stripeSubscriptionCreateResponse['id']);
    }

    /** @test */
    public function a_customer_swaps_subscriptions()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $initialActiveSubscription */
        $initialActiveSubscription = $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->subscribe($user, $subscriptionPlan);

        // Then
        $this->assertNotSame($initialActiveSubscription->subscriptionPlan->alias, $activeSubscription->subscriptionPlan->alias);
        $this->assertSame($activeSubscription->subscriptionPlan->alias, $subscriptionPlan->alias);
        $this->assertSame($activeSubscription->stripe_id, $this->stripeSubscriptionUpdateResponse['id']);
    }

    /** @test */
    public function a_customer_swaps_to_a_subscription_with_canceled_active_subscription()
    {
        // Given
        $this->expectException(ActiveSubscriptionStatusException::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $initialActiveSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
        $activeSubscription->deactivate();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        $subscriptionService->subscribe($user, $subscriptionPlan);
    }

    /** @test */
    public function a_customer_cancels_a_subscription()
    {
        // Given
        Event::fake(ActiveSubscriptionWasDeactivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->cancelSubscription($user, $activeSubscription);

        // Then
        $this->assertFalse($activeSubscription->fresh()->isSubscriptionActive());
        Event::assertDispatched(ActiveSubscriptionWasDeactivated::class);
    }

    /** @test */
    public function a_customer_cancels_an_already_cancelled_subscription()
    {
        // Given
        $this->expectException(ActiveSubscriptionStatusException::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
        $activeSubscription->deactivate();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        $subscriptionService->cancelSubscription($user, $activeSubscription);
    }

    /** @test */
    public function a_customer_reactivates_a_subscription()
    {
        // Given
        Event::fake(ActiveSubscriptionWasActivated::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        factory(Card::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
        $activeSubscription->deactivate();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->reactivateSubscription($user, $activeSubscription);

        // Then
        $this->assertTrue($activeSubscription->fresh()->isSubscriptionActive());
        Event::assertDispatched(ActiveSubscriptionWasActivated::class);
    }
}
