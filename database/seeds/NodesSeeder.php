<?php

use App\Models\Dialect;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Models\Translations\Translation;

class NodesSeeder extends AbstractSeeder
{
    /**
     * Defines the legth of the tree.
     *
     * @var string
     */
    const TREE_WIDTH = 3;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(['nodes']);

        $projects = Project::all();
        $dialects = Dialect::whereIn('locale', ['es_ES', 'en_US'])->get();

        $faker = Faker\Factory::create();

        /** @var \App\Models\Translations\Project $project */
        foreach ($projects as $project) {
            for ($x = 0; $x < self::TREE_WIDTH; $x++) {
                $rootKey = $faker->word;

                $root = Node::create([
                    'key'        => $rootKey,
                    'route'      => $rootKey,
                    'sort_index' => $x,
                    'project_id' => $project->id,
                ]);

                for ($i = 0; $i < self::TREE_WIDTH; $i++) {
                    $node1Key = $faker->word;

                    $node1 = Node::create([
                        'key'        => $node1Key,
                        'route'      => implode('.', [$root->key, $node1Key]),
                        'sort_index' => $i,
                        'project_id' => $project->id,
                    ], $root);

                    for ($m = 0; $m < self::TREE_WIDTH; $m++) {
                        $node2Key = $faker->word;

                        $node2 = Node::create([
                            'key'        => $node2Key,
                            'route'      => implode('.', [$root->key, $node1->key, $node2Key]),
                            'sort_index' => $m,
                            'project_id' => $project->id,
                        ], $node1);

                        foreach ($dialects as $dialect) {
                            factory(Translation::class)->create([
                                'node_id'    => $node2->id,
                                'dialect_id' => $dialect['dialect_id'],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
