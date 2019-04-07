<?php

namespace Tests\Unit\Listeners\User;

use App\Events\User\UserWasDeleted;
use App\Listeners\SendUserDeletionNotification;
use App\Mail\SendUserDeletionEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendUserDeletionNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_mail_is_send_for_user_deletion()
    {
        // Given
        Mail::fake(SendUserDeletionEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var UserWasDeleted $event */
        $event = new UserWasDeleted($user);

        /** @var \App\Listeners\SendUserDeactivationNotification $listener */
        $listener = new SendUserDeletionNotification();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendUserDeletionEmail::class);
    }
}