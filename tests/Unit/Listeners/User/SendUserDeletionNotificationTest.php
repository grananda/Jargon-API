<?php


namespace Tests\Unit\Listeners\User;


use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasDeactivated;
use App\Events\User\UserWasDeleted;
use App\Listeners\SendUserActivationNotification;
use App\Listeners\SendUserDeactivationNotification;
use App\Listeners\SendUserDeletionNotification;
use App\Mail\SendUserActivationEmail;
use App\Mail\SendUserDeactivationEmail;
use App\Mail\SendUserDeletionEmail;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendUserDeletionNotificationTest extends TestCase
{
    use RedirectsUsers;

    /** @test */
    public function a_mail_is_send_for_user_deactivation()
    {
        // Given
        Mail::fake(SendUserDeletionEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var UserActivationTokenGenerated $event */
        $event = new UserWasDeleted($user);

        /** @var \App\Listeners\SendUserDeactivationNotification $listener */
        $listener = new SendUserDeletionNotification();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendUserDeletionEmail::class);
    }
}