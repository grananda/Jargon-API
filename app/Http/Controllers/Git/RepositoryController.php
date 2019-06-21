<?php

namespace App\Http\Controllers\Git;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Repository\IndexRepositoryRequest;
use App\Services\GitHub\GitHubRepoService;
use Exception;

class RepositoryController extends ApiController
{
    /**
     * @var \App\Services\GitHub\GitHubRepoService
     */
    private $gitHubService;

    /**
     * RepositoryController constructor.
     *
     * @param \App\Services\GitHub\GitHubRepoService $gitHubRepository
     */
    public function __construct(GitHubRepoService $gitHubRepository)
    {
        $this->gitHubService = $gitHubRepository;
    }

    /**
     * @param \App\Http\Requests\Repository\IndexRepositoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRepositoryRequest $request)
    {
        try {
            $repositories = $this->gitHubService->getRepositoryList($request->project->gitConfig);

            return $this->responseOk($repositories);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
