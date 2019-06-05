<?php

namespace Tests\Feature\Customer;

use App\Models\User;
use App\Repositories\Stripe\StripeCustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group feature
 * @covers \App\Http\Controllers\Subscription\CustomerController::store
 */
class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array
     */
    private $stripeCustomerResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->stripeCustomerResponse = $this->loadFixture('stripe/customer.create.success');

        $this->mock(StripeCustomerRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('create')
                ->withArgs([User::class])
                ->andReturn($this->stripeCustomerResponse)
            ;
        });
    }

    /** @test */
    public function a_401_will_be_returned_if_the_user_is_not_logged_in()
    {
        // When
        $response = $this->post(route('customers.store'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_creating_an_existing_stripe_customer()
    {
        // Given
        /** @var \App\Models\User $usrer */
        $user = $this->user();

        // When
        $response = $this->signIn($user)->post(route('customers.store'));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_403_will_be_returned_when_a_creating_an_existing_stripe_customer_for_staff_member()
    {
        // Given
        /** @var \App\Models\User $usrer */
        $user = $this->staff('super-admin', [
            'stripe_id' => null,
        ]);

        // When
        $response = $this->signIn($user)->post(route('customers.store'));

        // Then
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_200_will_be_returned_when_a_stripe_customer_is_created()
    {
        // Given
        /** @var \App\Models\User $usrer */
        $user = $this->user('registered-user', [
            'stripe_id' => null,
        ]);

        // When
        $response = $this->signIn($user)->post(route('customers.store'));

        // Then
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSame($user->fresh()->stripe_id, $this->stripeCustomerResponse['id']);
    }
}
