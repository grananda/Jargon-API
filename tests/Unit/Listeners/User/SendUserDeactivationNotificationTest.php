<?php

namespace Tests\Unit\Listeners\User;

use App\Events\User\UserWasDeactivated;
use App\Listeners\SendUserDeactivationNotification;
use App\Mail\SendUserDeactivationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @group unit
 * @covers \App\Listeners\SendUserDeactivationNotification
 */
class SendUserDeactivationNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_mail_is_send_for_user_deactivation()
    {
        // Given
        Mail::fake(SendUserDeactivationEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var UserWasDeactivated $event */
        $event = new UserWasDeactivated($user);

        /** @var \App\Listeners\SendUserDeactivationNotification $listener */
        $listener = new SendUserDeactivationNotification();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendUserDeactivationEmail::class);
    }
}
