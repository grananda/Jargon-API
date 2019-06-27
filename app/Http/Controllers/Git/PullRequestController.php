<?php

namespace App\Http\Controllers\Git;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Git\PullRequestCreateRequest;
use App\Jobs\CreateProjectPullRequest;
use Exception;

class PullRequestController extends ApiController
{
    public function create(PullRequestCreateRequest $request)
    {
        try {
            CreateProjectPullRequest::dispatch($request->project);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
