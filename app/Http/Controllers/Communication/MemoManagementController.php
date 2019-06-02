<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Communication\DeleteMemoRequest;
use App\Http\Requests\Communication\DeleteMemoStaffRequest;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
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
     * @param \App\Http\Requests\Communication\DeleteMemoStaffRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
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
