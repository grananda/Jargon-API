<?php

namespace Tests\Feature\Option;


use App\Events\Option\OptionWasCreated;
use App\Events\Option\OptionWasUpdated;
use App\Events\SubscriptionPlanOptionWasUpdated;
use App\Models\Options\Option;
use App\Models\Subscriptions\SubscriptionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create();

        // When
        $response = $this->put(route('options.update', [$option->uuid]), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_a_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create();

        // When
        $response = $this->signIn($user)->put(route('options.update', [$option->uuid]), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create();

        // When
        $response = $this->signIn($user)->put(route('options.update', [$option->uuid]), []);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_422_code_will_be_returned_when_providing_a_forbidden_param()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create();

        $data = [
            'option_key'   => $this->faker->word,
        ];

        // When
        $response = $this->signIn($user)->put(route('options.update', [$option->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_updating_a_subscription_plan_option()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create();

        $data = [
            'option_value' => 10,
            'option_scope' => null,
        ];

        // When
        $response = $this->signIn($user)->put(route('options.update', [$option->uuid]), $data);

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['option_value' => $data['option_value']]);
        $response->assertJsonFragment(['option_scope' => $option->option_scope]);
    }
}