<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\Request;
use App\Models\User;

class StoreCustomerRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('registerCustomer', User::class);
    }
}
