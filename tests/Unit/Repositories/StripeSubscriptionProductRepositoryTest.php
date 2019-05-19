<?php

namespace Tests\Unit\Repositories;

use App\Models\Subscriptions\SubscriptionProduct;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group external
 * @coversNothing
 */
class StripeSubscriptionProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creates_updates_and_deletes_a_stripe_product()
    {
        // Given
        /** @var \App\Models\Subscriptions\SubscriptionProduct $product */
        $product = factory(SubscriptionProduct::class)->make();

        /** @var \App\Models\Subscriptions\SubscriptionProduct $productUpdated */
        $productUpdated = factory(SubscriptionProduct::class)->make([
            'alias' => $product->alias,
        ]);

        /** @var StripeSubscriptionProductRepository $repo */
        $repo = resolve(StripeSubscriptionProductRepository::class);

        // When
        $responseCreate = $repo->create($product);
        $responseUpdate = $repo->update($productUpdated);
        $responseDelete = $repo->delete($product);

        // Then
        $this->assertSame($responseCreate['name'], $product->title);
        $this->assertSame($responseUpdate['name'], $productUpdated->title);
        $this->assertTrue($responseDelete);
    }
}
