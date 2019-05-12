<?php

namespace App\Http\Controllers\Translations;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Translation\DeleteTranslationRequest;
use App\Http\Requests\Translation\IndexTranslationRequest;
use App\Http\Requests\Translation\StoreTranslationRequest;
use App\Http\Requests\Translation\UpdateTranslationRequest;
use App\Http\Resources\Translation as TranslationResource;
use App\Http\Resources\TranslationCollection;
use App\Repositories\TranslationRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class TranslationController extends ApiController
{
    /**
     * The TranslationRepository instance.
     *
     * @var TranslationRepository
     */
    private $translationRepository;

    /**
     * TranslationController constructor.
     *
     * @param TranslationRepository $translationRepository
     */
    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexTranslationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexTranslationRequest $request)
    {
        try {
            /** @var Collection $translations */
            $translations = $request->node->translations;

            return $this->responseOk(new TranslationCollection($translations));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Translation\StoreTranslationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTranslationRequest $request)
    {
        try {
            /** @var \App\Models\Translations\Translation $translation */
            $translation = $this->translationRepository->createTranslation($request->node, $request->validated());

            return $this->responseOk(new TranslationResource($translation));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Translation\UpdateTranslationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTranslationRequest $request)
    {
        try {
            /** @var \App\Models\Translations\Translation $translation */
            $translation = $this->translationRepository->update($request->translation, $request->validated());

            return $this->responseOk(new TranslationResource($translation));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteTranslationRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteTranslationRequest $request)
    {
        try {
            $this->translationRepository->delete($request->translation);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
