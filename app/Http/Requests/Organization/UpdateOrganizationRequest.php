<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\Request;
use App\Models\Organization;
use App\Policies\Traits\ActiveSubscriptionRestrictionsTrait;

class UpdateOrganizationRequest extends Request
{
    use ActiveSubscriptionRestrictionsTrait;

    /**
     * The organization instance.
     *
     * @var \App\Models\Organization
     */
    public $organization;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->organization = Organization::findByUuidOrFail($this->route('id'));

        return $this->hasActiveSubscription($this->user())
            && $this->user()->can('update', $this->organization);
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
