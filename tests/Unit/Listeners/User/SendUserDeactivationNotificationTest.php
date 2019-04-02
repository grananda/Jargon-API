<?php


namespace Tests\Unit\Listeners\User;


use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasDeactivated;
use App\Listeners\SendUserActivationNotification;
use App\Listeners\SendUserDeactivationNotification;
use App\Mail\SendUserActivationEmail;
use App\Mail\SendUserDeactivationEmail;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendUserDeactivationNotificationTest extends TestCase
{
    use RedirectsUsers;

    /** @test */
    public function a_mail_is_send_for_user_deactivation()
    {
        // Given
        Mail::fake(SendUserDeactivationEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var UserActivationTokenGenerated $event */
        $event = new UserWasDeactivated($user);

        /** @var \App\Listeners\SendUserDeactivationNotification $listener */
        $listener = new SendUserDeactivationNotification();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendUserDeactivationEmail::class);
    }
}