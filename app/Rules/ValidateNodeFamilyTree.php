<?php

namespace App\Rules;

use App\Models\Translations\Node;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidateNodeFamilyTree.
 *
 * @package App\Rules
 */
class ValidateNodeFamilyTree implements Rule
{
    /**
     * @var \App\Models\Translations\Node
     */
    protected $node;

    /**
     * Create a new rule instance.
     *
     * @param \App\Models\Translations\Node $node
     */
    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $ancestors   = array_flatten($this->node->ancestors->pluck('uuid'));
        $descendants = array_flatten($this->node->descendants->pluck('uuid'));

        return ! in_array($value, array_merge_recursive($ancestors, $descendants));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
