<?php


namespace Tests\Unit\Services;


use App\Jobs\UpdateStripeCustomer;
use App\Repositories\Stripe\StripeCustomerRepository;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
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
            ->willReturn($response);

        /** @var \App\Services\CustomerService $billingService */
        $billingService = new CustomerService($stripeCustomerRepository);

        // When
        /** @var \App\Models\User $user */
        $user = $billingService->registerCustomer($user);

        // Then
        $this->assertEquals($user->stripe_id, $response['id']);

        Bus::assertDispatched(UpdateStripeCustomer::class);
    }
}