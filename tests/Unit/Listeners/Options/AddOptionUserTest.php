<?php

namespace Tests\Unit\Listeners\Options;

use App\Events\Option\OptionWasCreated;
use App\Listeners\AddOptionUser;
use App\Models\Options\Option;
use App\Models\User;
use App\Repositories\OptionAppRepository;
use App\Repositories\OptionUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @coversNothing
 */
class AddOptionUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_new_option_is_added_to_user_options()
    {
        // Given
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

        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var AddOptionUser $listener */
        $listener = new AddOptionUser($optionUserRepository, $optionAppRepository);

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
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

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

        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var AddOptionUser $listener */
        $listener = new AddOptionUser($optionUserRepository, $optionAppRepository);

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

    /** @test */
    public function a_new_option_is_added_to_app_options()
    {
        // Given
        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \App\Events\Option\OptionWasCreated $event */
        $event = new OptionWasCreated($option);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var AddOptionUser $listener */
        $listener = new AddOptionUser($optionUserRepository, $optionAppRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertDatabaseHas('option_apps', [
            'option_key'   => $option->option_key,
            'option_value' => $option->option_value,
        ]);
        $this->assertDatabaseMissing('option_users', [
            'option_key'   => $option->option_key,
            'option_value' => $option->option_value,
        ]);
    }
}
