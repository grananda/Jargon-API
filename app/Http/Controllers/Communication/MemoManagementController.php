<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Communication\Staff\DeleteMemoStaffRequest;
use App\Http\Requests\Communication\Staff\IndexMemoStaffRequest;
use App\Http\Requests\Communication\Staff\ShowMemoStaffRequest;
use App\Http\Requests\Communication\Staff\StoreMemoStaffRequest;
use App\Http\Requests\Communication\Staff\UpdateMemoStaffRequest;
use App\Repositories\MemoRepository;
use Exception;
use Illuminate\Http\Request;

class MemoManagementController extends ApiController
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
     * @param \App\Http\Requests\Communication\Staff\IndexMemoStaffRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexMemoStaffRequest $request)
    {
        try {
            $memos = $this->memoRepository->findAllBy([]);

            return $this->responseOk($memos);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Communication\Staff\StoreMemoStaffRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMemoStaffRequest $request)
    {
        try {
            /** @var \App\Models\Communications\Memo $memo */
            $memo = $this->memoRepository->createMemo($request->validated());

            return $this->responseCreated($memo);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Communication\Staff\ShowMemoStaffRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowMemoStaffRequest $request)
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
     * @param \App\Http\Requests\Communication\Staff\UpdateMemoStaffRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMemoStaffRequest $request)
    {
        try {
            /** @var \App\Models\Communications\Memo $memo */
            $memo = $this->memoRepository->updateMemo($request->memo, $request->validated());

            return $this->responseOk($memo);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Communication\Staff\DeleteMemoStaffRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteMemoStaffRequest $request)
    {
        try {
            $this->memoRepository->delete($request->memo);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
