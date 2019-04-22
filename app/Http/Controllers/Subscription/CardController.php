<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Card\DeleteCardRequest;
use App\Http\Requests\Card\IndexCardRequest;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Http\Resources\Cards\Card as CardResource;
use App\Http\Resources\Cards\CardCollection;
use App\Repositories\CardRepository;
use App\Services\CardService;
use Exception;

class CardController extends ApiController
{
    /**
     * @var \App\Services\CardService
     */
    private $cardService;

    /**
     * @var \App\Repositories\CardRepository
     */
    private $cardRepository;

    /**
     * CardController constructor.
     *
     * @param \App\Services\CardService        $cardService
     * @param \App\Repositories\CardRepository $cardRepository
     */
    public function __construct(CardService $cardService, CardRepository $cardRepository)
    {
        $this->cardService    = $cardService;
        $this->cardRepository = $cardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Card\IndexCardRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexCardRequest $request)
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Collection $cards */
            $cards = $this->cardRepository->findAllBy(['user_id', $request->user()->id]);

            return $this->responseOk(new CardCollection($cards));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Card\StoreCardRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCardRequest $request)
    {
        try {
            /** @var \App\Models\Card $card */
            $card = $this->cardService->registerCard($request->user(), $request->get('stripeCardToken'));

            return $this->responseCreated(new CardResource($card));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Card\UpdateCardRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCardRequest $request)
    {
        try {
            /** @var \App\Models\Card $card */
            $card = $this->cardService->updateCard($request->card, $request->validated());

            return $this->responseOk(new CardResource($card));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Card\DeleteCardRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteCardRequest $request)
    {
        try {
            $this->cardService->deleteCard($request->card);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
