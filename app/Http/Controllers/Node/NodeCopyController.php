<?php

namespace App\Http\Controllers\Node;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Node\NodeCopyRequest;

/**
 * Class NodeCopyController.
 *
 * @package App\Http\Controllers\Api\Node
 */
class NodeCopyController extends ApiController
{
    /**
     * NodeCopyController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Node\NodeCopyRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NodeCopyRequest $request)
    {
        return $this->responseOk([]);
    }
}
