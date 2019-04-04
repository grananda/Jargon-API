<?php


namespace Tests\Feature\User;


use App\Events\User\UserWasDeleted;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\traits\CreateActiveSubscription;
use Tests\traits\CreateOptionUsers;

class DeleteTest extends TestCase
{
    use RefreshDatabase,
        CreateActiveSubscription,
        CreateOptionUsers;

    /** @test */
    public function a_200_will_be_returned_when_a_user_is_created()
    {
        // Given
        Event::fake(UserWasDeleted::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var \App\Models\Subscriptions\ActiveSubscription $subcription */
        $subcription = $this->createActiveSubscription($user, SubscriptionPlan::DEFAULT_SUBSCRIPTION_PLAN);

        /** @var \Illuminate\Database\Eloquent\Collection $userOptions */
        $userOptions = $this->createUserOptions($user);

        /** @var \App\Models\User $user */
        $staff = $this->staff(User::SENIOR_STAFF_MEMBER);

        // When
        $response = $this->signIn($staff)->delete(route('users.destroy', [$user->uuid]));

        // Then
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('users', [
            'uuid' => $user->uuid,
        ]);

        $this->assertDatabaseMissing('option_users', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('active_subscriptions', [
            'id'      => $subcription->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('active_subscription_option_values', [
            'user_id'         => $user->id,
            'subscription_id' => $subcription->id,
        ]);

        Event::assertDispatched(UserWasDeleted::class);
    }
}