<?php

namespace App\Http\Requests\SubscriptionPlan;

use App\Http\Requests\Request;
use App\Models\Currency;
use App\Models\Subscriptions\SubscriptionPlan;
use App\Models\Subscriptions\SubscriptionProduct;
use App\Rules\ValidCurrencyCode;
use App\Rules\ValidSubscriptionProduct;
use Illuminate\Validation\Rule;

class StoreSubscriptionPlanRequest extends Request
{
    /**
     * @var \App\Models\Subscriptions\SubscriptionProduct
     */
    public $product;

    /**
     * @var \App\Models\Currency
     */
    public $currency;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->product = SubscriptionProduct::findByUuid($this->input('product'));

        $this->currency = Currency::where('code', $this->input('currency'))->firstOrFail();

        return $this->user()->can('create', SubscriptionPlan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency'   => ['required', 'string', new ValidCurrencyCode()],
            'product'    => ['required', 'string', new ValidSubscriptionProduct()],
            'alias'      => ['required', 'string'],
            'interval'   => ['required', Rule::in(['month', 'year'])],
            'sort_order' => ['sometimes', 'numeric'],
            'amount'     => ['required', 'numeric'],
            'is_active'  => ['sometimes', 'boolean'],
            'options'    => ['required', 'array'],
            'options.*'  => ['required'],
        ];
    }
}
