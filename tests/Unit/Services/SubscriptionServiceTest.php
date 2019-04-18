<?php


namespace Tests\Unit\Services;


use App\Exceptions\ActiveSubscriptionStatusException;
use App\Exceptions\StripeCardTokenMissingException;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\ActiveSubscriptionRepository;
use App\Repositories\Stripe\StripeCardRepository;
use App\Repositories\Stripe\StripeCustomerRepository;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /**
     * @var array
     */
    private $stripeSubscriptionResponse;

    /**
     * @var array
     */
    private $stripeCustomerResponse;

    /**
     * @var array
     */
    private $stripeCardResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->stripeSubscriptionResponse = $this->loadFixture('stripe/subscription.create.success');

        $this->stripeCustomerResponse = $this->loadFixture('stripe/customer.create.success');

        $this->stripeCardResponse = $this->loadFixture('stripe/card.create.success');

        $this->mock(StripeSubscriptionRepository::class, function ($mock) {
            /** @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withAnyArgs()
                ->andReturn($this->stripeSubscriptionResponse);
        });

        $this->mock(StripeCustomerRepository::class, function ($mock) {
            /** @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withAnyArgs()
                ->andReturn($this->stripeCustomerResponse);
        });

        $this->mock(StripeCardRepository::class, function ($mock) {
            /** @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withAnyArgs()
                ->andReturn($this->stripeCardResponse);
        });
    }

    /** @test */
    public function a_customer_subscribes_to_a_subscription()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        $stripeCardToken = 'tok_visa';

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->subscribe($user, $subscriptionPlan, $stripeCardToken);

        // Then
        $this->assertEquals($activeSubscription->subscriptionPlan->alias, $subscriptionPlan->alias);
        $this->assertEquals($activeSubscription->stripe_id, $this->stripeSubscriptionResponse['id']);
        $this->assertTrue((bool)$user->fresh()->cards->count());
    }

    /** @test */
    public function a_customer_does_not_subscribes_to_a_subscription_without_cc()
    {
        // Given
        $this->expectException(StripeCardTokenMissingException::class);

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
    public function a_customer_subscribes_to_a_subscription_from_free()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN, [], [
            'stripe_id' => null,
            'ends_at'   => null,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create();

        /** @var \App\Services\SubscriptionService $subscriptionService */
        $subscriptionService = resolve(SubscriptionService::class);

        $stripeCardToken = 'tok_visa';

        // When
        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $subscriptionService->subscribe($user, $subscriptionPlan, $stripeCardToken);

        // Then
        $this->assertEquals($activeSubscription->subscriptionPlan->alias, $subscriptionPlan->alias);
        $this->assertEquals($activeSubscription->stripe_id, $this->stripeSubscriptionResponse['id']);
    }
//
//    /** @test */
//    public function a_customer_swaps_subscriptions()
//    {
//        // Given
//        /** @var \App\Models\User $user */
//        $user = $this->user();
//
//        /** @var \App\Models\Subscriptions\ActiveSubscription $initialActiveSubscription */
//        $initialActiveSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
//
//        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
//        $subscriptionPlan = factory(SubscriptionPlan::class)->create();
//
//        /** @var array $stripeResponse */
//        $stripeResponse = $this->loadFixture('stripe/subscription.update.success');
//
//        $stripeSubscriptionRepository = $this->createMock(StripeSubscriptionRepository::class);
//        $stripeSubscriptionRepository->method('swap')
//            ->willReturn($stripeResponse);
//
//        /** @var \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository */
//        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);
//
//        /** @var \App\Services\SubscriptionService $subscriptionService */
//        $subscriptionService = new SubscriptionService($activeSubscriptionRepository, $stripeSubscriptionRepository, ,);
//
//        // When
//        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
//        $activeSubscription = $subscriptionService->subscribe($user, $subscriptionPlan);
//
//        // Then
//        $this->assertNotEquals($initialActiveSubscription->subscriptionPlan->alias, $activeSubscription->subscriptionPlan->alias);
//        $this->assertEquals($activeSubscription->subscriptionPlan->alias, $subscriptionPlan->alias);
//        $this->assertEquals($activeSubscription->stripe_id, $stripeResponse['id']);
//    }
//
//    /** @test */
//    public function a_customer_swaps_to_a_subscription_with_canceled_active_subscription()
//    {
//        // Given
//        $this->expectException(ActiveSubscriptionStatusException::class);
//
//        /** @var \App\Models\User $user */
//        $user = $this->user();
//
//        /** @var \App\Models\Subscriptions\ActiveSubscription $initialActiveSubscription */
//        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
//        $activeSubscription->deactivate();
//
//        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
//        $subscriptionPlan = factory(SubscriptionPlan::class)->create();
//
//        /** @var array $stripeResponse */
//        $stripeResponse = $this->loadFixture('stripe/subscription.update.success');
//
//        $stripeSubscriptionRepository = $this->createMock(StripeSubscriptionRepository::class);
//        $stripeSubscriptionRepository->method('swap')
//            ->willReturn($stripeResponse);
//
//        /** @var \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository */
//        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);
//
//        /** @var \App\Services\SubscriptionService $subscriptionService */
//        $subscriptionService = new SubscriptionService($activeSubscriptionRepository, $stripeSubscriptionRepository, ,);
//
//        // When
//        $subscriptionService->subscribe($user, $subscriptionPlan);
//    }
//
//    /** @test */
//    public function a_customer_cancels_a_subscription()
//    {
//        // Given
//        /** @var \App\Models\User $user */
//        $user = $this->user();
//
//        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
//        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
//
//        /** @var array $stripeResponse */
//        $stripeResponse = $this->loadFixture('stripe/subscription.cancel.success');
//
//        $stripeSubscriptionRepository = $this->createMock(StripeSubscriptionRepository::class);
//        $stripeSubscriptionRepository->method('cancel')
//            ->willReturn($stripeResponse);
//
//        /** @var \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository */
//        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);
//
//        /** @var \App\Services\SubscriptionService $subscriptionService */
//        $subscriptionService = new SubscriptionService($activeSubscriptionRepository, $stripeSubscriptionRepository, ,);
//
//        // When
//        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
//        $activeSubscription = $subscriptionService->cancelSubscription($user, $activeSubscription);
//
//        // Then
//        $this->assertFalse($activeSubscription->fresh()->isSubscriptionActive());
//    }
//
//    /** @test */
//    public function a_customer_cancels_an_already_cancelled_subscription()
//    {
//        // Given
//        $this->expectException(ActiveSubscriptionStatusException::class);
//
//        /** @var \App\Models\User $user */
//        $user = $this->user();
//
//        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
//        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
//        $activeSubscription->deactivate();
//
//        /** @var array $stripeResponse */
//        $stripeResponse = $this->loadFixture('stripe/subscription.cancel.success');
//
//        $stripeSubscriptionRepository = $this->createMock(StripeSubscriptionRepository::class);
//        $stripeSubscriptionRepository->method('cancel')
//            ->willReturn($stripeResponse);
//
//        /** @var \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository */
//        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);
//
//        /** @var \App\Services\SubscriptionService $subscriptionService */
//        $subscriptionService = new SubscriptionService($activeSubscriptionRepository, $stripeSubscriptionRepository, ,);
//
//        // When
//        $subscriptionService->cancelSubscription($user, $activeSubscription);
//    }
//
//    /** @test */
//    public function a_customer_reactivates_a_subscription()
//    {
//        // Given
//        /** @var \App\Models\User $user */
//        $user = $this->user();
//
//        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
//        $activeSubscription = $this->createActiveSubscription($user, 'professional-month-eur');
//        $activeSubscription->deactivate();
//
//        /** @var array $stripeResponse */
//        $stripeResponse = $this->loadFixture('stripe/subscription.reactivate.success');
//
//        $stripeSubscriptionRepository = $this->createMock(StripeSubscriptionRepository::class);
//        $stripeSubscriptionRepository->method('reactivate')
//            ->willReturn($stripeResponse);
//
//        /** @var \App\Repositories\ActiveSubscriptionRepository $activeSubscriptionRepository */
//        $activeSubscriptionRepository = resolve(ActiveSubscriptionRepository::class);
//
//        /** @var \App\Services\SubscriptionService $subscriptionService */
//        $subscriptionService = new SubscriptionService($activeSubscriptionRepository, $stripeSubscriptionRepository, ,);
//
//        // When
//        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
//        $activeSubscription = $subscriptionService->reactivateSubscription($user, $activeSubscription);
//
//        // Then
//        $this->assertTrue($activeSubscription->fresh()->isSubscriptionActive());
//    }
}