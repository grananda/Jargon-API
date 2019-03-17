<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;

class StoreOrganizationRequest extends Request
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->hasActiveSubscription($this->user())
            && $this->user()->can('create', Organization::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
