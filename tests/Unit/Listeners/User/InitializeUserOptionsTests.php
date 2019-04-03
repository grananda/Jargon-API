<?php


namespace Tests\Unit\Listeners\User;


use App\Events\User\UserWasActivated;
use App\Listeners\InitializeUserOptions;
use App\Models\Options\Option;
use App\Repositories\OptionUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitializeUserOptionsTests extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function option_users_are_created_when_activating_a_user()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Events\User\UserWasActivated $event */
        $event = new UserWasActivated($user);

        /** @var OptionUserRepository $optionUserRepository */
        $optionUserRepository = resolve(OptionUserRepository::class);

        /** @var \App\Listeners\InitializeUserOptions $listener */
        $listener = new InitializeUserOptions($optionUserRepository);

        // When
        $listener->handle($event);

        // Then
        $this->assertEquals($user->fresh()->options()->count(), Option::where('option_scope', Option::USER_OPTION)->count());
    }
}