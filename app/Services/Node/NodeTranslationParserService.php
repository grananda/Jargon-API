<?php

namespace App\Services\Node;

use App\Models\Dialect;
use App\Models\Translations\JargonOption;
use App\Models\Translations\Node;

class NodeTranslationParserService
{
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
