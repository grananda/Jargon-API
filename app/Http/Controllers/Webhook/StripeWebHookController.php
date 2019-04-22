<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\VerifyWebhookSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StripeWebHookController extends ApiController
{
    /**
     * Create a new webhook controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('services.stripe.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a Stripe webhook call.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $type = $payload['type'] ?? null;

        if (empty($payload) || ! $type) {
            return $this->responseBadRequest(trans('Payload is invalid!'));
        }

        $jobClassName = '\\App\\Jobs\\Stripe\\'.Str::studly(str_replace('.', '_', $type));

        if (class_exists($jobClassName)) {
            $data = array_merge($payload['data']['object'], [
                'previous_attributes' => Arr::get($payload, 'data.previous_attributes', []),
            ]);

            dispatch(new $jobClassName($data));
        }

        return $this->responseOk('Ok!');
    }
}
