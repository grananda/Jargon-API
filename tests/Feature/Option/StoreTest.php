<?php

namespace Tests\Feature\Option;

use App\Events\Option\OptionWasCreated;
use App\Models\Options\Option;
use App\Models\Options\OptionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Option\OptionController::store
 */
class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('options.store'), []);

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
        $response = $this->signIn($user)->post(route('options.store'), []);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_if_the_user_is_not_a_senior_staff_member()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->staff(User::JUNIOR_STAFF_MEMBER);

        // When
        $response = $this->signIn($user)->post(route('options.store'), []);

        // Assert
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_code_will_be_returned_when_creating_a_subscription_plan_option()
    {
        // Given
        /* @var \App\Models\User $user */
        Event::fake(OptionWasCreated::class);

        $user = $this->staff(User::SENIOR_STAFF_MEMBER);

        $optionKey = $this->faker->word;

        /** @var \App\Models\Options\OptionCategory $cat */
        $optionCategory = OptionCategory::inRandomOrder()->first();

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->make([
            'option_key'         => $optionKey,
            'option_category_id' => $optionCategory->uuid,
        ]);

        // When
        $response = $this->signIn($user)->post(route('options.store'), $option->toArray());

        // Then
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(['option_key' => $option->option_key]);
        $this->assertDatabaseHas('options', [
            'option_key'         => $option->option_key,
            'option_category_id' => $optionCategory->id,
        ]);

        Event::assertDispatched(OptionWasCreated::class);
    }
}
