<?php

namespace App\Http\Controllers\Node;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Node\RelocateNodeRequest;
use App\Http\Resources\Node as NodeResource;
use App\Models\Translations\Node;
use App\Services\Node\NodeRelocationService;
use Illuminate\Http\JsonResponse;
use Throwable;

class NodeRelocationController extends ApiController
{
    /**
     * The NodeRelocationService instance.
     *
     * @var NodeRelocationService
     */
    private $nodeRelocationService;

    /**
     * NodeRelocationController constructor.
     *
     * @param NodeRelocationService $nodeRelocationService
     */
    public function __construct(NodeRelocationService $nodeRelocationService)
    {
        $this->nodeRelocationService = $nodeRelocationService;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RelocateNodeRequest $request
     *
     * @return JsonResponse
     */
    public function update(RelocateNodeRequest $request)
    {
        try {
            /** @var Node $node */
            $node = $this->nodeRelocationService->relocateNode($request->node, $request->parent);

            return $this->responseOk(new NodeResource($node));
        } catch (Throwable $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
