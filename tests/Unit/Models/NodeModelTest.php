<?php

namespace Tests\Unit\Models;

use App\Models\Dialect;
use App\Models\Translations\Node;
use App\Models\Translations\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @coversNothing
 */
class NodeModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_correct_translation_is_returned()
    {
        // Given
        /** @var \App\Models\Translations\Node $node */
        $node = factory(Node::class)->create();

        /** @var \App\Models\Dialect $dialect1 */
        $dialect1 = Dialect::inRandomOrder()->first();

        /** @var \App\Models\Dialect $dialect2 */
        $dialect2 = Dialect::inRandomOrder()->first();

        $translation1 = factory(Translation::class)->create([
            'node_id'    => $node->id,
            'dialect_id' => $dialect1->id,
        ]);

        $translation2 = factory(Translation::class)->create([
            'node_id'    => $node->id,
            'dialect_id' => $dialect2->id,
        ]);

        // When
        $response = $node->findTranslation($dialect1);

        // then
        $this->assertEquals($translation1->definition, $response);
    }

    /** @test */
    public function no_translation_is_returned()
    {
        // Given
        /** @var \App\Models\Translations\Node $node */
        $node = factory(Node::class)->create();

        /** @var \App\Models\Dialect $dialect1 */
        $dialect1 = Dialect::inRandomOrder()->first();

        /** @var \App\Models\Dialect $dialect2 */
        $dialect2 = Dialect::inRandomOrder()->first();

        $translation1 = factory(Translation::class)->create([
            'node_id'    => $node->id,
            'dialect_id' => $dialect1->id,
        ]);

        // When
        $response = $node->findTranslation($dialect2);

        // then
        $this->assertFalse($response);
    }
}
