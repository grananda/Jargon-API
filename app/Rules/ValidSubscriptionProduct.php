<?php

namespace App\Rules;

use App\Models\Subscriptions\SubscriptionProduct;
use Illuminate\Contracts\Validation\Rule;

class ValidSubscriptionProduct implements Rule
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
        return (bool) SubscriptionProduct::findByUuid($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Invalid product provided.');
    }
}
