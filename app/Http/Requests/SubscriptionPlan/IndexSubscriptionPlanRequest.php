<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Http\Requests\Request;

class IndexSubscriptionPlanRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
