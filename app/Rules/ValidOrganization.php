<?php

namespace App\Rules;

use App\Models\Organization;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidOrganization.
 *
 * @package App\Rules
 */
class ValidOrganization implements Rule
{
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
        return Organization::where('uuid', $value)->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid organization supplied.';
    }
}
