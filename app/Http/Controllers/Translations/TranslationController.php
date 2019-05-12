<?php

namespace App\Http\Controllers\Translations;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Translation\DeleteTranslationRequest;
use App\Http\Requests\Translation\IndexTranslationRequest;
use App\Http\Requests\Translation\ShowTranslationRequest;
use App\Http\Resources\TranslationCollection;
use App\Repositories\TranslationRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

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
     * @param IndexTranslationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Translation\ShowTranslationRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowTranslationRequest $request)
    {
        try {
            return $request->translation;
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
