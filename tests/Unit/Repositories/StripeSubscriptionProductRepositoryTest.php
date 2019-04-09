<?php


namespace Tests\Unit\Repositories;


use App\Models\Subscriptions\SubscriptionProduct;
use App\Repositories\Stripe\StripeSubscriptionProductRepository;
use Tests\TestCase;

/** @group using-external-service */
class StripeSubscriptionProductRepositoryTest extends TestCase
{
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
        $this->assertEquals($responseCreate['name'], $product->title);
        $this->assertEquals($responseUpdate['name'], $productUpdated->title);
        $this->assertTrue($responseDelete);
    }
}