<?php

namespace Tests\Feature;

use App\Models\Options\Option;
use App\Models\User;
use App\Repositories\OptionRepository;
use App\Repositories\OptionUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_user_options_for_new_user()
    {
        // Given
        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'user',
        ]);

        // When
        $optionUserRepository->createUserOptions($user);

        // Then
        foreach ($options as $option) {
            $this->assertDatabaseHas('option_users', [
                'option_key' => $option->option_key,
                'user_id'    => $user->id,
            ]);
        }
    }

    /** @test */
    public function remove_user_options()
    {
        // Given
        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'user',
        ]);

        $optionUserRepository->createUserOptions($user);

        // When
        $optionUserRepository->removeUserOptions($user);

        // Then
        foreach ($options as $option) {
            $this->assertDatabaseMissing('option_users', [
                'option_key' => $option->option_key,
                'user_id'    => $user->id,
            ]);
        }
    }

    /** @test */
    public function update_user_options()
    {
        // Given
        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'user',
        ]);

        $optionUserRepository->createUserOptions($user);

        $optionKey = $options->first()->option_key;
        $optionValue = $this->faker->word;

        $attributes = [
            $optionKey => $optionValue,
        ];

        // When
        $optionUserRepository->updateUserOptions($user, $attributes);

        // Then
        $this->assertDatabaseHas('option_users', [
            'option_key'   => $optionKey,
            'option_value' => $optionValue,
            'user_id'      => $user->id,
        ]);
    }

    /** @test */
    public function rebuild_user_options()
    {
        // Given
        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'user',
        ]);

        // When
        $optionUserRepository->rebuildUserOptions($user);

        // Then
        foreach ($options as $option) {
            $this->assertDatabaseHas('option_users', [
                'option_key' => $option->option_key,
                'user_id'    => $user->id,
            ]);
        }
    }
}
