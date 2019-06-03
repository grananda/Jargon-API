<?php

namespace Tests\Feature\SubscriptionPlan;

use App\Events\SubscriptionPlan\SubscriptionPlanWasCreated;
use App\Events\SubscriptionPlan\SubscriptionPlanWasUpdated;
use App\Models\Subscriptions\SubscriptionOption;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group feature
 * @coversNothing
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('subscriptions.plans.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        // When
        $response = $this->signIn($user)->post(route('subscriptions.plans.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var \App\Models\Subscriptions\SubscriptionProduct $product */
        $product = factory(SubscriptionProduct::class)->create();

        /** @var array $data */
        $data = factory(SubscriptionPlan::class)->make([
            'product'  => $product->uuid,
            'currency' => 'EUR',
        ])->toArray();

        // When
        $response = $this->signIn($user)->post(route('subscriptions.plans.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_if_the_user_is_a_staff_member()
    {
        // Given
        Event::fake([SubscriptionPlanWasUpdated::class, SubscriptionPlanWasCreated::class]);

        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var \App\Models\Subscriptions\SubscriptionOption $option */
        $option = factory(SubscriptionOption::class)->create();

        /** @var \App\Models\Subscriptions\SubscriptionProduct $product */
        $product = factory(SubscriptionProduct::class)->create();

        /** @var \App\Models\Subscriptions\SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = factory(SubscriptionPlan::class)->make([
            'currency' => 'EUR',
            'product'  => $product->uuid,
        ]);

        $optionValue = $this->faker->numberBetween(5, 10);

        /** @var array $data */
        $data = array_merge($subscriptionPlan->toArray(), [
            'options' => [
                [
                    'option_value' => $optionValue,
                    'option_key'   => $option->option_key,
                ],
            ],
        ]);

        // When
        $response = $this->signIn($user)->post(route('subscriptions.plans.store'), $data);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['alias' => $data['alias']]);

        $subscriptionPlan = SubscriptionPlan::findByUuidOrFail($response->json()['data']['id']);

        $this->assertDatabaseHas('subscription_plans', [
            'alias' => $subscriptionPlan->alias,
        ]);
        $this->assertDatabaseHas('subscription_plan_option_values', [
            'subscription_plan_id' => $subscriptionPlan->id,
            'option_key'           => $option->option_key,
            'option_value'         => $optionValue,
        ]);

        Event::assertDispatched(SubscriptionPlanWasCreated::class);
        Event::assertNotDispatched(SubscriptionPlanWasUpdated::class);
    }
}
