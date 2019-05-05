<?php

namespace Tests\Unit\Repositories;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasDeleted;
use App\Events\SubscriptionProduct\SubscriptionProductWasCreated;
use App\Events\SubscriptionProduct\SubscriptionProductWasDeleted;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Repositories\Stripe\StripeCardRepository;
use App\Repositories\Stripe\StripeCustomerRepository;
use App\Repositories\Stripe\StripeInvoiceRepository;
use App\Repositories\Stripe\StripeSubscriptionPlanRepository;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use App\Repositories\Stripe\StripeSubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;

/**
 * @coversNothing
 */
class StripeInvoiceRepositoryTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

    /** @test */
    public function a_customer_invoice_is_retrieved()
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

        /** @var StripeCardRepository $stripeCardRepository */
        $stripeCardRepository = resolve(StripeCardRepository::class);

        /** @var \App\Repositories\Stripe\StripeInvoiceRepository $stripeInvoiceRespository */
        $stripeInvoiceRespository = resolve(StripeInvoiceRepository::class);

        // *** Create objects ***

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create([
            'amount' => 20,
            'alias'  => 'Plan-1',
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $subscriptionPlan->product;

        // *** Create Stripe elements ***

        /** @var array $customer */
        $customer = $stripeCustomerRepository->create($user);
        $user->update(['stripe_id' => $customer['id']]);
        $user->refresh();

        /** @var array $card */
        $card = $stripeCardRepository->create($user, 'tok_visa');

        /** @var array $product */
        $product = $stripeSubscriptionProductRepository->create($subscriptionProduct);

        /** @var array $plan */
        $plan = $stripeSubscriptionPlanRepository->create($subscriptionPlan);

        // When
        /** @var array $responseCreate */

        // *** Create subscription ***

        $responseCreate = $stripeSubscriptionRepository->create($user, $subscriptionPlan);
        $this->createActiveSubscription($user, $subscriptionPlan->alias, [], ['stripe_id' => $responseCreate['id']]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $user->fresh()->activeSubscription;

        $responseInvoices = $stripeInvoiceRespository->list($user);

        // *** Cleanup ***

        $activeSubscription->delete();
        $stripeCustomerRepository->delete($user);

        $stripeSubscriptionPlanRepository->delete($subscriptionPlan);
        $subscriptionPlan->delete();

        $stripeSubscriptionProductRepository->delete($subscriptionProduct);
        $subscriptionProduct->delete();

        // Then
        $this->assertSame($responseCreate['customer'], $user->stripe_id);
        $this->assertSame($responseCreate['plan']['id'], $subscriptionPlan->alias);
        $this->assertSame($responseInvoices['data'][0]['customer'], $user->stripe_id);
    }
}
