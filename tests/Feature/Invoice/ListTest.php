<?php

namespace Tests\Feature\Invoice;

use App\Models\User;
use App\Repositories\Stripe\StripeInvoiceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * group feature.
 *
 * @covers \App\Http\Controllers\Subscription\InvoiceController
 */
class ListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_401_is_returned_when_not_logged_in()
    {
        // When
        $response = $this->get(route('invoices.index'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_200_is_returned_when_requesting_invoices()
    {
        // Given
        $this->mock(StripeInvoiceRepository::class, function ($mock) {
            /* @var \Mockery\Mock $mock */
            $mock->shouldReceive('list')
                ->withArgs([User::class])
                ->once()
                ->andReturn($this->loadFixture('stripe/invoice.list.success'))
            ;
        });

        /** @var \App\Models\User $user1 */
        $user1 = $this->user();

        // When
        $response = $this->signIn($user1)->get(route('invoices.index'));

        // Assert
        $response->assertStatus(Response::HTTP_OK);
    }
}
