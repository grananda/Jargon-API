<?php

namespace App\Http\Controllers\Node;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Node\CopyNodeRequest;
use App\Http\Resources\Node as NodeResource;
use App\Models\Translations\Node;
use App\Services\Node\NodeDuplicationService;
use Illuminate\Http\JsonResponse;
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
     * @var NodeDuplicationService
     */
    private $nodeDuplicationService;

    /**
     * NodeCopyController constructor.
     *
     * @param NodeDuplicationService $nodeDuplicationService
     */
    public function __construct(NodeDuplicationService $nodeDuplicationService)
    {
        $this->nodeDuplicationService = $nodeDuplicationService;
    }

    /**
     * Update a node by uuid.
     *
     * @param CopyNodeRequest $request
     *
     * @return JsonResponse
     */
    public function update(CopyNodeRequest $request)
    {
        try {
            /** @var Node $node */
            $node = $this->nodeDuplicationService->copyNode($request->node, $request->parent);

            return $this->responseOk(new NodeResource($node));
        } catch (Throwable $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
