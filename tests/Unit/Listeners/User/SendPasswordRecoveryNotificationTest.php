<?php


namespace Tests\Unit\Listeners\User;


use App\Events\User\PasswordResetRequested;
use App\Listeners\SendPasswordRecoveryNotification;
use App\Mail\SendPasswordRecoveryEmail;
use App\Models\PasswordReset;
use App\Repositories\PasswordResetRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendPasswordRecoveryNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_mail_is_send_for_password_recovery()
    {
        // Given
        Event::fake(PasswordResetRequested::class);
        Mail::fake(SendPasswordRecoveryEmail::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var PasswordResetRepository $passwordResetRepository */
        $passwordResetRepository = resolve(PasswordResetRepository::class);

        /** @var array $passwordResetArray */
        $token = $passwordResetRepository->createPasswordReset($user);

        /** @var \App\Models\PasswordReset $passwordReset */
        $passwordReset = $passwordResetArray['entity'];


        /** @var PasswordResetRequested $event */
        $event = new PasswordResetRequested($passwordReset, $token);

        /** @var SendPasswordRecoveryNotification $listener */
        $listener = new SendPasswordRecoveryNotification();

        // When
        $listener->handle($event);

        // Then
        Event::assertDispatched(PasswordResetRequested::class);
        Mail::assertSent(SendPasswordRecoveryEmail::class);
    }
}