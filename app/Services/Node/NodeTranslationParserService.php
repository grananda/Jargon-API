<?php

namespace App\Services\Node;

use App\Models\Dialect;
use App\Models\Translations\JargonOption;
use App\Models\Translations\Node;
use App\Models\Translations\Project;
use App\Repositories\DialectRepository;
use App\Repositories\NodeRepository;
use App\Repositories\TranslationRepository;

class NodeTranslationParserService
{
    /**
     * @var \App\Repositories\DialectRepository
     */
    private $dialectRepository;

    /**
     * @var \App\Services\Node\NodeService
     */
    private $nodeService;

    /**
     * @var \App\Repositories\TranslationRepository
     */
    private $translationRepository;

    /**
     * @var \App\Repositories\NodeRepository
     */
    private $nodeRepository;

    /**
     * NodeTranslationParserService constructor.
     *
     * @param \App\Repositories\DialectRepository     $dialectRepository
     * @param \App\Services\Node\NodeService          $nodeService
     * @param \App\Repositories\TranslationRepository $translationRepository
     * @param \App\Repositories\NodeRepository        $nodeRepository
     */
    public function __construct(
        DialectRepository $dialectRepository,
        NodeService $nodeService,
        TranslationRepository $translationRepository,
        NodeRepository $nodeRepository
    ) {
        $this->dialectRepository     = $dialectRepository;
        $this->nodeService           = $nodeService;
        $this->translationRepository = $translationRepository;
        $this->nodeRepository        = $nodeRepository;
    }

    /**
     * Parses a root node into a translation file.
     *
     * @param \App\Models\Translations\Node         $rootNode
     * @param \App\Models\Dialect                   $dialect
     * @param \App\Models\Dialect                   $defaultDialect
     * @param \App\Models\Translations\JargonOption $options
     *
     * @return array
     */
    public function parseTranslationFile(Node $rootNode, Dialect $dialect, Dialect $defaultDialect, JargonOption $options): array
    {
        if ($options->translation_file_mode === 'array') {
            $arr = $this->treeToArray($rootNode, $dialect, $defaultDialect);
            $arr = $this->parseCodeToString($arr);
        } else {
            $arr = $this->treeToJson($rootNode, $dialect, $defaultDialect);
        }

        $file = "<?php\n\nreturn {$arr};\n";

        return
            [
                'locale'  => $dialect->locale,
                'path'    => $options->i18n_path.$dialect->locale,
                'file'    => $rootNode->key.'.'.$options->file_ext,
                'content' => $file,
                'hash'    => sha1($file),
            ];
    }

    /**
     * Return translation tree with desired translation by dialect.
     *
     * @param \App\Models\Translations\Node $parentNode
     * @param \App\Models\Dialect           $dialect
     * @param \App\Models\Dialect           $defaultDialect
     *
     * @return array
     */
    public function treeToArray(Node $parentNode, Dialect $dialect, Dialect $defaultDialect): array
    {
        $arr = [];

        foreach ($parentNode->descendants()->get() as $node) {
            /** @var \App\Models\Translations\Node $node */
            if ($node->translations()->count() > 0) {
                $translation = $node->findTranslation($dialect);

                if (! $translation) {
                    $translation = $node->findTranslation($defaultDialect);
                }

                if (! $translation) {
                    $translation = $node->route;
                }

                data_fill($arr, $node->route, $translation);
            }

            if ($node->children()->count() > 0) {
                data_fill($arr, $node->route, []);
            }
        }

        return $arr;
    }

    /**
     * Converts node tree to json.
     *
     * @param \App\Models\Translations\Node $parentNode
     * @param \App\Models\Dialect           $dialect
     * @param \App\Models\Dialect           $defaultDialect
     *
     * @return false|string
     */
    public function treeToJson(Node $parentNode, Dialect $dialect, Dialect $defaultDialect): string
    {
        return json_encode($this->treeToArray($parentNode, $dialect, $defaultDialect));
    }

    /**
     * Persists a json node tree into a project.
     *
     * @param \App\Models\Translations\Project $project
     * @param string                           $json
     *
     * @return \App\Models\Translations\Project
     */
    public function jsonToTree(Project $project, string $json)
    {
        /** @var array $arr */
        $arr = json_decode($json, true);

        /** @var array $file */
        foreach ($arr['data'] as $file) {
            /** @var array $item */
            foreach ($file as $item) {
                /** @var \App\Models\Dialect $dialect */
                $dialect = $this->dialectRepository->findByOrFail(['locale' => $item['locale']]);

                $nodeProcessor = function ($key, $value, $parent = null) use ($project, $dialect, &$nodeProcessor) {
                    /** @var \App\Models\Translations\Node $node */
                    $node = $this->processNode($project, $key, $parent);

                    if (is_array($value)) {
                        foreach ($value as $key => $_value) {
                            $value['parent'] = $node;

                            $nodeProcessor($key, $_value, $value['parent']);
                        }
                    } else {
                        $this->processTranslation($node, $dialect, $value);
                    }
                };

                $nodeProcessor(key($item['content']), value(current($item['content'])));
            }
        }

        return $project->refresh();
    }

    /**
     * @param \App\Models\Translations\Project   $project
     * @param \App\Models\Translations\Node|null $parent
     * @param string                             $value
     *
     * @throws \Throwable
     *
     * @return \App\Models\Translations\Node
     */
    private function processNode(Project $project, string $value, Node $parent = null): Node
    {
        /** @var \App\Models\Translations\Node $node */
        $node = $this->nodeRepository->findBy([
            'project_id' => $project->id,
            'key'        => $value,
        ]);

        if (! $node) {
            $node = $this->nodeService->storeNode($project, $parent,
                [
                    'key' => $value,
                ]
            );
        }

        return $node;
    }

    /**
     * @param \App\Models\Translations\Node $node
     * @param \App\Models\Dialect           $dialect
     * @param string                        $value
     *
     * @throws \Throwable
     */
    private function processTranslation(Node $node, Dialect $dialect, string $value): void
    {
        /** @var \App\Models\Translations\Translation $translation */
        $translation = $this->translationRepository->create(
            [
                'definition' => value($value),
            ]
        );

        $translation->node()->associate($node);
        $translation->dialect()->associate($dialect);

        $translation->save();
    }

    /**
     * Converts array to text.
     *
     * @param array $arr
     *
     * @return mixed
     */
    private function parseCodeToString(array $arr)
    {
        $arr = var_export($arr, true);

        $arr = str_replace('array (', '[', $arr);
        $arr = str_replace(')', ']', $arr);

        return str_replace("=> \n", "=>\n", $arr);
    }
}
