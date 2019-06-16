<?php

namespace App\Http\Controllers\Git;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Branch\DeleteBranchRequest;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Repositories\GitHub\GitHubRepository;
use Exception;

class BranchController extends ApiController
{
    /**
     * @var \App\Repositories\GitHub\GitHubRepository
     */
    private $gitHubRepository;

    /**
     * RepositoryController constructor.
     *
     * @param \App\Repositories\GitHub\GitHubRepository $gitHubRepository
     */
    public function __construct(GitHubRepository $gitHubRepository)
    {
        $this->gitHubRepository = $gitHubRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Branch\StoreBranchRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBranchRequest $request)
    {
        try {
            $branch = $this->gitHubRepository->createBranch($request->project, $request->branch);

            return $this->responseCreated($branch);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Branch\DeleteBranchRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteBranchRequest $request)
    {
        try {
            $this->gitHubRepository->removeBranch($request->project, $request->branch);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
