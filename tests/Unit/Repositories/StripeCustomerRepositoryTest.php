<?php


namespace Tests\Unit\Repositories;


use App\Repositories\Stripe\StripeCustomerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeCustomerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creates_updates_and_deletes_a_stripe_customer()
    {
        // Given
        $email1 = $this->faker->email;

        /** @var \App\Models\User $user */
        $user = $this->user('registered-user', ['email' => $email1]);

        /** @var \App\Repositories\Stripe\StripeCustomerRepository $stripeCustomerRepo */
        $stripeCustomerRepo = resolve(StripeCustomerRepository::class);

        // When
        /** @var array $responseCustomerCreated */
        $responseCustomerCreated = $stripeCustomerRepo->create($user);

        $email2 = $this->faker->email;
        $user->update([
            'stripe_id' => $responseCustomerCreated['id'],
            'email'     => $email2,
        ]);
        $user->refresh();

        /** @var array $responseCustomerUpdated */
        $responseCustomerUpdated = $stripeCustomerRepo->update($user);

        /** @var bool $responseCustomerDeleted */
        $responseCustomerDeleted = $stripeCustomerRepo->delete($user);

        // Then
        $this->assertEquals($responseCustomerCreated['email'], $email1);
        $this->assertEquals($responseCustomerUpdated['email'], $email2);
        $this->assertTrue($responseCustomerDeleted);
    }
}