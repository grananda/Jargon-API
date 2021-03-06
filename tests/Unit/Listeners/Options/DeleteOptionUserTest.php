<?php

namespace Tests\Unit\Listeners\Options;

use App\Events\Option\OptionWasDeleted;
use App\Listeners\DeleteOptionUser;
use App\Models\Options\Option;
use App\Repositories\OptionAppRepository;
use App\Repositories\OptionUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Listeners\DeleteOptionUser
 */
class DeleteOptionUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_option_is_deleted_from_user()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Options\Option $option1 */
        $option1 = factory(Option::class)->create([
            'option_scope' => 'user',
        ]);

        /** @var \App\Models\Options\Option $option2 */
        $option2 = factory(Option::class)->create([
            'option_scope' => 'user',
        ]);

        /** @var \App\Repositories\OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        $optionUserRepository->createUserOption($user, [
            'option_value' => $option1->option_value,
            'option_key'   => $option1->option_key,
        ]);

        $optionUserRepository->createUserOption($user, [
            'option_value' => $option2->option_value,
            'option_key'   => $option2->option_key,
        ]);

        /** @var \App\Events\Option\OptionWasDeleted $event */
        $event = new OptionWasDeleted($option1);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var DeleteOptionUser $listener */
        $listener = new DeleteOptionUser($optionUserRepository, $optionAppRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertDatabaseMissing('option_users', [
            'user_id'      => $user->id,
            'option_key'   => $option1->option_key,
            'option_value' => $option1->option_value,
        ]);
        $this->assertDatabaseHas('option_users', [
            'user_id'      => $user->id,
            'option_key'   => $option2->option_key,
            'option_value' => $option2->option_value,
        ]);
    }

    /** @test */
    public function an_option_is_deleted_from_app()
    {
        // Given
        /** @var \App\Models\Options\Option $option1 */
        $option1 = factory(Option::class)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \App\Models\Options\Option $option2 */
        $option2 = factory(Option::class)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \App\Repositories\OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        $optionAppRepository->create([
            'option_value' => $option1->option_value,
            'option_key'   => $option1->option_key,
        ]);

        $optionAppRepository->create([
            'option_value' => $option2->option_value,
            'option_key'   => $option2->option_key,
        ]);

        /** @var \App\Events\Option\OptionWasDeleted $event */
        $event = new OptionWasDeleted($option1);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var DeleteOptionUser $listener */
        $listener = new DeleteOptionUser($optionUserRepository, $optionAppRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertDatabaseMissing('option_apps', [
            'option_key'   => $option1->option_key,
            'option_value' => $option1->option_value,
        ]);
        $this->assertDatabaseHas('option_apps', [
            'option_key'   => $option2->option_key,
            'option_value' => $option2->option_value,
        ]);
    }
}
