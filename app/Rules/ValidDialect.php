<?php

namespace App\Rules;

use App\Models\Dialect;
use Illuminate\Contracts\Validation\Rule;

class ValidDialect implements Rule
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
        return (bool) Dialect::where('locale', $value)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Invalid dialect provided.');
    }
}
