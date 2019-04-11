<?php


namespace Tests\Unit\Repositories;


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

class StripeSubscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription;

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

        /** @var \App\Repositories\Stripe\StripeSubscriptionProductRepository $stripeSubscriptionProductRepository */
        $stripeSubscriptionProductRepository = resolve(StripeSubscriptionProductRepository::class);

        /** @var \App\Repositories\Stripe\StripeSubscriptionPlanRepository $stripeSubscriptionPlanRepository */
        $stripeSubscriptionPlanRepository = resolve(StripeSubscriptionPlanRepository::class);

        /** @var \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepository */
        $stripeCustomerRepository = resolve(StripeCustomerRepository::class);

        /** @var \App\Repositories\Stripe\StripeSubscriptionRepository $stripeSubscriptionRepository */
        $stripeSubscriptionRepository = resolve(StripeSubscriptionRepository::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->create([
            'amount' => 0,
        ]);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $subscriptionProduct */
        $subscriptionProduct = $subscriptionPlan->product;

        /** @var array $customer */
        $customer = $stripeCustomerRepository->create($user);
        $user->update(['stripe_id' => $customer['id']]);
        $user->refresh();

        /** @var array $product */
        $product = $stripeSubscriptionProductRepository->create($subscriptionProduct);

        /** @var array $plan */
        $plan = $stripeSubscriptionPlanRepository->create($subscriptionPlan);

        // When
        /** @var array $responseCreated */
        $responseCreated = $stripeSubscriptionRepository->create($user, $subscriptionPlan);
        $this->createActiveSubscription($user, $subscriptionPlan->alias, [], ['stripe_id' => $responseCreated['id']]);

        /** @var \App\Models\Subscriptions\ActiveSubscription $activeSubscription */
        $activeSubscription = $user->fresh()->activeSubscription;

        /** @var array $responseCanceled */
        $responseCancelled = $stripeSubscriptionRepository->cancel($user, $activeSubscription);
        $activeSubscription->update(['ends_at' => $responseCancelled['cancel_at']]);
        $activeSubscription->refresh();

        /** @var array $responseReactivate */
        $responseReactivated = $stripeSubscriptionRepository->reactivate($user, $activeSubscription);
        $activeSubscription->update(['ends_at' => $responseCancelled['cancel_at']]);
        $activeSubscription->refresh();

        $activeSubscription->delete();
        $stripeCustomerRepository->delete($user);
        $stripeSubscriptionPlanRepository->delete($subscriptionPlan);
        $subscriptionPlan->delete();
        $stripeSubscriptionProductRepository->delete($subscriptionProduct);

        // Then
        $this->assertEquals($responseCreated['customer'], $user->stripe_id);
        $this->assertNotNull($responseCancelled['cancel_at']);
        $this->assertNull($responseReactivated['cancel_at']);
    }

    /** @test */
    public function creates_a_paid_stripe_subscription()
    {

    }

    /** @test */
    public function fails_to_create_a_paid_stripe_subscription()
    {

    }

    /** @test */
    public function swaps_to_a_paid_stripe_subscription()
    {

    }

    /** @test */
    public function fails_to_swap_to_a_paid_stripe_subscription()
    {

    }

    /** @test */
    public function swaps_to_a_free_stripe_subscription()
    {

    }
}