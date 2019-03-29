<?php

namespace Tests\Unit\Listeners\Options;


use App\Events\Option\OptionWasCreated;
use App\Listeners\AddOptionUser;
use App\Models\Options\Option;
use App\Models\User;
use App\Repositories\OptionUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AddOptionUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_option_is_added_to_user_options()
    {
        // Given
        Event::fake(OptionWasCreated::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create([
            'option_scope' => 'user',
        ]);

        /** @var \App\Events\Option\OptionWasCreated $event */
        $event = new OptionWasCreated($option);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var AddOptionUser $listener */
        $listener = new AddOptionUser($optionUserRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertDatabaseHas('option_users', [
            'user_id'      => $user->id,
            'option_key'   => $option->option_key,
            'option_value' => $option->option_value,
        ]);
        $this->assertDatabaseMissing('option_users', [
            'user_id'      => $staff->id,
            'option_key'   => $option->option_key,
            'option_value' => $option->option_value,
        ]);
    }

    /** @test */
    public function a_new_app_option_is_not_added_to_user_options()
    {
        Event::fake(OptionWasCreated::class);

        // Given
        /** @var \App\Models\User $user */
        $user = factory(User::class)->create();

        /** @var \App\Models\User $staff */
        $staff = $this->staff();

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \App\Events\Option\OptionWasCreated $event */
        $event = new OptionWasCreated($option);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var AddOptionUser $listener */
        $listener = new AddOptionUser($optionUserRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertDatabaseMissing('option_users', [
            'user_id'      => $staff->id,
            'option_key'   => $option->option_key,
            'option_value' => $option->option_value,
        ]);
        $this->assertDatabaseMissing('option_users', [
            'user_id'      => $user->id,
            'option_key'   => $option->option_key,
            'option_value' => $option->option_value,
        ]);
    }
}
