<?php

namespace App\Rules;

use App\Models\Currency;
use Illuminate\Contracts\Validation\Rule;

class ValidCurrencyCode implements Rule
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
        return (bool) Currency::where('code', $value)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Invalid currency provided.');
    }
}
