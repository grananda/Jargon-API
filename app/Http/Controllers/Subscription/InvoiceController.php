<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Invoice\IndexInvoiceRequest;
use App\Repositories\Stripe\StripeInvoiceRepository;
use Exception;

class InvoiceController extends ApiController
{
    /**
     * @var \App\Repositories\Stripe\StripeInvoiceRepository
     */
    private $invoiceRepository;

    /**
     * InvoiceController constructor.
     *
     * @param \App\Repositories\Stripe\StripeInvoiceRepository $invoiceRepository
     */
    public function __construct(StripeInvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param \App\Http\Requests\Invoice\IndexInvoiceRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexInvoiceRequest $request)
    {
        try {
            $invoices = $this->invoiceRepository->list($request->user());

            return $this->responseOk($invoices);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
