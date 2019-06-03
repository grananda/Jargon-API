<?php

namespace Tests\Unit\Listeners\User;

use App\Events\User\UserActivationTokenGenerated;
use App\Listeners\SendUserActivationNotification;
use App\Mail\SendUserActivationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class SendUserActivationNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_mail_is_send_for_user_activation()
    {
        // Given
        Mail::fake(SendUserActivationEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var UserActivationTokenGenerated $event */
        $event = new UserActivationTokenGenerated($user);

        /** @var \App\Listeners\SendUserActivationNotification $listener */
        $listener = new SendUserActivationNotification();

        // When
        $listener->handle($event);

        // Then
        Mail::assertSent(SendUserActivationEmail::class);
    }
}
