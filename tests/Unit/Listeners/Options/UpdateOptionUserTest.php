<?php

namespace Tests\Unit\Listeners\Options;


use App\Events\Option\OptionWasUpdated;
use App\Listeners\UpdateOptionUser;
use App\Models\Options\Option;
use App\Repositories\OptionUserRepository;
use Tests\TestCase;

class UpdateOptionUserTest extends TestCase
{
    /** @test */
    public function an_option_is_updated()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Options\Option $option */
        $option = factory(Option::class)->create([
            'option_scope' => 'user',
        ]);

        /** @var \App\Repositories\OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        $optionUserRepository->createUserOption($user, [
            'option_value' => $option->option_value,
            'option_key'   => $option->option_key,
        ]);

        /** @var \App\Models\Options\Option $params */
        $newOption = factory(Option::class)->make(array_merge($option->toArray(), [
            'option_value'=>$this->faker->word,
            ]
        ));

        $newOptionArray = $newOption->toArray();

        /** @var \App\Events\Option\OptionWasUpdated $event */
        $event = new OptionWasUpdated($newOption);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var \App\Listeners\UpdateOptionUser $listener */
        $listener = new UpdateOptionUser($optionUserRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertDatabaseMissing('option_users', [
            'user_id'      => $user->id,
            'option_key'   => $option->option_key,
            'option_value' => $newOptionArray['option_value'],
        ]);
    }
}
