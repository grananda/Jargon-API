<?php

namespace Tests\Unit\Services;

use App\Jobs\UpdateStripeCustomer;
use App\Repositories\Stripe\StripeCustomerRepository;
use App\Services\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * @group unit
 * @coversNothing
 */
class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_registered_as_a_stripe_customer()
    {
        // Given
        Bus::fake(UpdateStripeCustomer::class);

        /** @var \App\Models\User $user */
        $user = $this->user();

        /** @var array $response */
        $response = $this->loadFixture('stripe/customer.create.success');

        $stripeCustomerRepository = $this->createMock(StripeCustomerRepository::class);
        $stripeCustomerRepository->method('create')
            ->willReturn($response)
        ;

        /** @var \App\Services\CustomerService $customerService */
        $customerService = new CustomerService($stripeCustomerRepository);

        // When
        /** @var \App\Models\User $user */
        $user = $customerService->registerCustomer($user);

        // Then
        $this->assertSame($user->stripe_id, $response['id']);

        Bus::assertNotDispatched(UpdateStripeCustomer::class);
    }
}
