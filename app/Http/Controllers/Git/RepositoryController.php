<?php

namespace App\Http\Controllers\Git;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Repository\IndexRepositoryRequest;
use App\Repositories\GitHub\GitHubRepoRepository;
use Exception;

class RepositoryController extends ApiController
{
    /**
     * @var \App\Repositories\GitHub\GitHubRepoRepository
     */
    private $gitHubRepository;

    /**
     * RepositoryController constructor.
     *
     * @param \App\Repositories\GitHub\GitHubRepoRepository $gitHubRepository
     */
    public function __construct(GitHubRepoRepository $gitHubRepository)
    {
        $this->gitHubRepository = $gitHubRepository;
    }

    /**
     * @param \App\Http\Requests\Repository\IndexRepositoryRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRepositoryRequest $request)
    {
        try {
            $repositories = $this->gitHubRepository->getRepositoryList($request->project);

            return $this->responseOk($repositories);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
