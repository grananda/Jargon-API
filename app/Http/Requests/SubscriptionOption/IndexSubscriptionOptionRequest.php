<?php

namespace App\Http\Requests\SubscriptionOption;

use App\Http\Requests\Request;
use App\Models\Subscriptions\SubscriptionOption;

class IndexSubscriptionOptionRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', SubscriptionOption::class);
    }
}
