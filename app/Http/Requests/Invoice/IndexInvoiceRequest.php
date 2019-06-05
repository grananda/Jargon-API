<?php

namespace App\Http\Requests\Invoice;

use App\Http\Requests\Request;

class IndexInvoiceRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isStripeCustomer();
    }
}
