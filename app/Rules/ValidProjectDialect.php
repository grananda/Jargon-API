<?php

namespace App\Rules;

use App\Models\Translations\Project;
use Illuminate\Contracts\Validation\Rule;

class ValidProjectDialect implements Rule
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
        return (bool) $this->project->dialects()->where('locale', $value)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Provided dialect nor allowed un project');
    }
}
