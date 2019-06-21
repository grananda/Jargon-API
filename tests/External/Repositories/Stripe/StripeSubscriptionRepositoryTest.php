<?php

namespace Tests\External\Repositories\Stripe;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripeCustomerRepository;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @group external
 * @covers \App\Repositories\Stripe\StripeSubscriptionRepository
 */
class StripeSubscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use CreateActiveSubscription;

    /** @test */
    public function creates_updates_cancels_and_reactivates_a_stripe_subscription()
    {
        // Given
        Event::fake([
            SubscriptionProductWasCreated::class,
            SubscriptionPlanWasCreated::class,
            SubscriptionPlanWasDeleted::class,
            SubscriptionProductWasDeleted::class,
        ]);

        // *** Resolve repositories ***

        /** @var \App\Repositories\Stripe\StripeSubscriptionProductRepository $stripeSubscriptionProductRepository */
        $stripeSubscriptionProductRepository = resolve(StripeSubscriptionProductRepository::class);

        /** @var \App\Repositories\Stripe\StripeSubscriptionPlanRepository $stripeSubscriptionPlanRepository */
        $stripeSubscriptionPlanRepository = resolve(StripeSubscriptionPlanRepository::class);

        /** @var \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository */
        $stripeCustomerRepository = resolve(StripeCustomerRepository::class);

        /** @var \App\Repositories\Stripe\StripeSubscriptionRepository $stripeSubscriptionRepository */
        $stripeSubscriptionRepository = resolve(StripeSubscriptionRepository::class);

        // *** Create objects ***

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create([
            'amount' => 0,
            'alias'  => 'Plan-1',
        ]);

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan2 = factory(SubscriptionPlan::class)->create([
            'amount' => 0,
            'alias'  => 'Plan-2',
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $subscriptionPlan->product;

        $subscriptionPlan2->product()->associate($subscriptionProduct);

        // *** Create Stripe elements ***

        /** @var array $customer */
        $customer = $stripeCustomerRepository->create($user);
        $user->update(['stripe_id' => $customer['id']]);
        $user->refresh();

        /** @var array $product */
        $product = $stripeSubscriptionProductRepository->create($subscriptionProduct);

        /** @var array $plan */
        $plan = $stripeSubscriptionPlanRepository->create($subscriptionPlan);

        /** @var array $plan2 */
        $plan2 = $stripeSubscriptionPlanRepository->create($subscriptionPlan2);

        // When
        /** @var array $responseCreate */

        // *** Create subscription ***

        $responseCreate = $stripeSubscriptionRepository->create($user, $subscriptionPlan);
        $this->createActiveSubscription($user, $subscriptionPlan->alias, [], ['stripe_id' => $responseCreate['id']]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $user->fresh()->activeSubscription;

        // *** Swap subscription ***

        /** @var array $responseUpdate */
        $responseUpdate = $stripeSubscriptionRepository->swap($user, $subscriptionPlan2);
        $activeSubscription->update(['stripe_id' => $responseUpdate['id']]);
        $activeSubscription->refresh();

        // *** Cancel subscription ***

        /** @var array $responseCanceled */
        $responseCancel = $stripeSubscriptionRepository->cancel($user, $activeSubscription);
        $activeSubscription->update([
            'ends_at' => $responseCancel['cancel_at'],
        ]);
        $activeSubscription->refresh();

        /** @var array $responseReactivate */
        $responseReactivate = $stripeSubscriptionRepository->reactivate($user, $activeSubscription);
        $activeSubscription->update([
            'stripe_id' => $responseReactivate['id'],
            'ends_at'   => $responseReactivate['cancel_at'],
        ]);
        $activeSubscription->refresh();

        // *** Cleanup ***

        $activeSubscription->delete();
        $stripeCustomerRepository->delete($user);

        $stripeSubscriptionPlanRepository->delete($subscriptionPlan);
        $subscriptionPlan->delete();

        $stripeSubscriptionPlanRepository->delete($subscriptionPlan2);
        $subscriptionPlan2->delete();

        $stripeSubscriptionProductRepository->delete($subscriptionProduct);
        $subscriptionProduct->delete();

        // Then
        $this->assertSame($responseCreate['customer'], $user->stripe_id);
        $this->assertSame($responseCreate['plan']['id'], $subscriptionPlan->alias);
        $this->assertSame($responseUpdate['plan']['id'], $subscriptionPlan2->alias);
        $this->assertNotNull($responseCancel['cancel_at']);
        $this->assertNull($responseReactivate['cancel_at']);
    }
}
