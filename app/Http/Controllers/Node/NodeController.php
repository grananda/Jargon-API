<?php

namespace App\Http\Controllers\Node;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Node\DeleteNodeRequest;
use App\Http\Requests\Node\IndexNodeRequest;
use App\Http\Requests\Node\StoreNodeRequest;
use App\Http\Requests\Node\UpdateNodeRequest;
use App\Http\Resources\Node as NodeResource;
use App\Http\Resources\NodeCollection;
use App\Repositories\NodeRepository;
use App\Services\NodeService;
use Exception;
use Throwable;

/**
 * Class NodeController.
 *
 * @package App\Http\Controllers\Api\Node
 */
class NodeController extends ApiController
{
    /**
     * The NodeRepository instance.
     *
     * @var \App\Repositories\NodeRepository
     */
    protected $nodeRepository;

    /**
     * The TranslationNodeSortingService instance.
     *
     * @var \App\Services\NodeSortingService
     */
    protected $nodeService;

    /**
     * NodeController constructor.
     *
     * @param \App\Repositories\NodeRepository $nodeRepository
     * @param \App\Services\NodeService        $nodeService
     */
    public function __construct(NodeRepository $nodeRepository, NodeService $nodeService)
    {
        $this->nodeRepository = $nodeRepository;

        $this->nodeService = $nodeService;
    }

    /**
     * Get all parent nodes by project.
     *
     * @param \App\Http\Requests\Node\IndexNodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexNodeRequest $request)
    {
        try {
            $nodes = $request->project->rootNodes;

            return $this->responseOk(new NodeCollection($nodes));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Creates a new node.
     *
     * @param \App\Http\Requests\Node\StoreNodeRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreNodeRequest $request)
    {
        try {
            /** @var \App\Models\Translations\Node $node */
            $node = $this->nodeService->storeNode($request->project, $request->parentNode, $request->all());

            return $this->responseOk(new NodeResource($node));
        } catch (Throwable $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update a node by uuid.
     *
     * @param \App\Http\Requests\Node\UpdateNodeRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateNodeRequest $request)
    {
        try {
            $node = $this->nodeRepository->update($request->node, $request->all());

            return $this->responseOk(new NodeResource($node));
        } catch (Throwable $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Deletes a node.
     *
     * @param \App\Http\Requests\Node\DeleteNodeRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteNodeRequest $request)
    {
        try {
            $this->nodeService->deleteNode($request->node);

            return $this->responseNoContent();
        } catch (Throwable $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
