<?php

namespace App\Http\Controllers\Node;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Node\CopyNodeRequest;
use App\Http\Resources\Node as NodeResource;
use App\Services\Node\NodeDuplicationService;
use Throwable;

/**
 * Class NodeCopyController.
 *
 * @package App\Http\Controllers\Api\Node
 */
class NodeDuplicationController extends ApiController
{
    /**
     * The NodeService instance.
     *
     * @var \App\Services\Node\NodeDuplicationService
     */
    private $nodeDuplicationService;

    /**
     * NodeCopyController constructor.
     *
     * @param \App\Services\Node\NodeDuplicationService $nodeDuplicationService
     */
    public function __construct(NodeDuplicationService $nodeDuplicationService)
    {
        $this->nodeDuplicationService = $nodeDuplicationService;
    }

    /**
     * Update a node by uuid.
     *
     * @param \App\Http\Requests\Node\CopyNodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CopyNodeRequest $request)
    {
        try {
            /** @var \App\Models\Translations\Node $node */
            $node = $this->nodeDuplicationService->copyNode($request->node, $request->parent);

            return $this->responseOk(new NodeResource($node));
        } catch (Throwable $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
