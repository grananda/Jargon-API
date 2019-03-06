<?php

namespace App\Rules;

use App\Models\Translations\Node;
use App\Models\Translations\Project;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidProjectNode.
 *
 * @package App\Rules
 */
class ValidProjectNode implements Rule
{
    /**
     * The Project instance.
     *
     * @var \App\Models\Translations\Project
     */
    protected $project;

    /**
     * Create a new rule instance.
     *
     * @param \App\Models\Translations\Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
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
        /** @var \App\Models\Translations\Node $node */
        $node = Node::findByUuidOrFail($value);

        return $node->project->uuid == $this->project->uuid;
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
