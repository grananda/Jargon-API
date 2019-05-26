<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Communication\DeleteMemoRequest;
use App\Http\Requests\Communication\IndexMemoRequest;
use App\Http\Requests\Communication\ShowMemoRequest;
use App\Http\Requests\Communication\UpdateMemoRequest;
use App\Repositories\MemoRepository;
use Exception;

class MemoController extends ApiController
{
    /**
     * The MemoRepository instance.
     *
     * @var \App\Repositories\MemoRepository
     */
    private $memoRepository;

    /**
     * MemoController constructor.
     *
     * @param \App\Repositories\MemoRepository $memoRepository
     */
    public function __construct(MemoRepository $memoRepository)
    {
        $this->memoRepository = $memoRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Communication\IndexMemoRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function index(IndexMemoRequest $request)
    {
        try {
            $memos = $request->user()->memos;

            return $this->responseOk($memos);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Communication\ShowMemoRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowMemoRequest $request)
    {
        try {
            return $this->responseOk($request->memo);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Communication\UpdateMemoRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMemoRequest $request)
    {
        try {
            /** @var \App\Models\Communications\Memo $memo */
            $memo = $this->memoRepository->update($request->memo, $request->validated());

            return $this->responseOk($memo);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Communication\DeleteMemoRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteMemoRequest $request)
    {
        try {
            $this->memoRepository->delete($request->memo);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
