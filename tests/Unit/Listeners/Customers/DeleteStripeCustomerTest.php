<?php

namespace Tests\Unit\Listeners\Customers;

use App\Events\User\UserWasDeleted;
use App\Listeners\DeleteStripeCustomer;
use App\Models\User;
use App\Repositories\Stripe\StripeCustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class DeleteStripeCustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_stripe_customer_is_deleted()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user();

        $this->mock(StripeCustomerRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('delete')
                ->withArgs([User::class])
                ->once()
                ->andReturn($this->loadFixture('stripe/customer.delete.success'))
            ;
        });

        /** @var \App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated $event */
        $event = new UserWasDeleted($user);

        /** @var \App\Listeners\CancelStripeSubscription $listener */
        $listener = resolve(DeleteStripeCustomer::class);

        // When
        $listener->handle($event);
    }

    /** @test */
    public function a_stripe_customer_is_not_deleted()
    {
        // Given
        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', [
            'stripe_id' => null,
        ]);

        $this->mock(StripeCustomerRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('delete')
                ->withArgs([User::class])
                ->never()
                ->andReturn($this->loadFixture('stripe/customer.delete.success'))
            ;
        });

        /** @var \App\Events\ActiveSubscription\ActiveSubscriptionWasDeactivated $event */
        $event = new UserWasDeleted($user);

        /** @var \App\Listeners\CancelStripeSubscription $listener */
        $listener = resolve(DeleteStripeCustomer::class);

        // When
        $listener->handle($event);
    }
}
