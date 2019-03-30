<?php

namespace Tests\Feature;

use App\Models\Options\Option;
use App\Models\User;
use App\Repositories\OptionAppRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OptionUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionAppRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_app_options()
    {
        // Given
        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        factory(Option::class, 5)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'staff',
        ]);

        // When
        $optionAppRepository->createUserOptions();

        // Then
        foreach ($options as $option) {
            $this->assertDatabaseHas('option_apps', [
                'option_key' => $option->option_key,
            ]);
        }
    }

    /** @test */
    public function remove_user_options()
    {
        // Given
        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        factory(Option::class, 5)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'staff',
        ]);

        $optionAppRepository->createUserOptions();

        // When
        $optionAppRepository->removeUserOptions();

        // Then
        foreach ($options as $option) {
            $this->assertDatabaseMissing('option_apps', [
                'option_key' => $option->option_key,
            ]);
        }
    }

    /** @test */
    public function update_user_options()
    {
        // Given
        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        factory(Option::class, 5)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'staff',
        ]);

        $optionAppRepository->createUserOptions();

        $optionKey = $options->first()->option_key;
        $optionValue = $this->faker->word;

        $attributes = [
            $optionKey => $optionValue,
        ];

        // When
        $optionAppRepository->updateUserOptions($attributes);

        // Then
        $this->assertDatabaseHas('option_apps', [
            'option_key'   => $optionKey,
            'option_value' => $optionValue,
        ]);
    }

    /** @test */
    public function rebuild_user_options()
    {
        // Given
        /** @var OptionAppRepository $optionAppRepository */
        $optionAppRepository = resolve(OptionAppRepository::class);

        /** @var OptionRepository $optionsRepository */
        $optionsRepository = resolve(OptionRepository::class);

        factory(Option::class, 5)->create([
            'option_scope' => 'staff',
        ]);

        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = $optionsRepository->findAllBy([
            'option_scope' => 'staff',
        ]);

        // When
        $optionAppRepository->rebuildAppOptions();

        // Then
        foreach ($options as $option) {
            $this->assertDatabaseHas('option_apps', [
                'option_key' => $option->option_key,
            ]);
        }
    }
}
