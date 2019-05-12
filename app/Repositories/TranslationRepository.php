<?php

namespace App\Repositories;

use App\Models\Translations\Node;
use App\Models\Translations\Translation;
use Illuminate\Database\Connection;

/**
 * Class NodeRepository.
 *
 * @package App\Repositories
 */
class TranslationRepository extends CoreRepository
{
    /**
     * @var \App\Repositories\DialectRepository
     */
    private $dialectTransformer;

    /**
     * Node Repository constructor.
     *
     * @param \Illuminate\Database\Connection     $dbConnection
     * @param Translation                         $model
     * @param \App\Repositories\DialectRepository $dialectTransformer
     */
    public function __construct(Connection $dbConnection, Translation $model, DialectRepository $dialectTransformer)
    {
        parent::__construct($dbConnection, $model);

        $this->dialectTransformer = $dialectTransformer;
    }

    public function createTranslation(Node $node, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($node, $attributes) {
            /** @var \App\Models\Translations\Translation $translation */
            $translation = $this->create($attributes);

            /** @var \App\Models\Dialect $dialect */
            $dialect = $this->dialectTransformer->findBy(['locale' => $attributes['dialect']]);

            $dialect->translations()->save($translation);

            $node->translations()->save($translation);

            return $translation->fresh();
        });
    }
}
