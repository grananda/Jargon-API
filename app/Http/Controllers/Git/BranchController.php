<?php

namespace App\Http\Controllers\Git;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Branch\DeleteBranchRequest;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Services\GitHub\GitHubBranchService;
use Exception;

class BranchController extends ApiController
{
    /**
     * @var \App\Services\GitHub\GitHubBranchService
     */
    private $gitHubService;

    /**
     * RepositoryController constructor.
     *
     * @param \App\Services\GitHub\GitHubBranchService $gitHubRepository
     */
    public function __construct(GitHubBranchService $gitHubRepository)
    {
        $this->gitHubService = $gitHubRepository;
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
            $branch = $this->gitHubService->createBranch($request->project->gitConfig, $request->branch);

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
            $this->gitHubService->removeBranch($request->project->gitConfig, $request->branch);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
