<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Project\IndexProjectRequest;
use App\Http\Requests\Project\ShowProjectRequest;
use App\Http\Resources\Projects\Project as ProjectResource;
use App\Http\Resources\Projects\ProjectCollection;
use App\Repositories\ProjectRepository;
use Exception;
use Illuminate\Http\Request;

class ProjectController extends ApiController
{
    /**
     * The ProjectRepository instance.
     *
     * @var \App\Repositories\ProjectRepository
     */
    protected $projectRepository;

    /**
     * ProjectController constructor.
     *
     * @param \App\Repositories\ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Project\IndexProjectRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexProjectRequest $request)
    {
        try {
            $projects = $this->projectRepository->findAllByMember($request->user());

            return $this->responseOk(new ProjectCollection($projects));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Project\ShowProjectRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowProjectRequest $request)
    {
        try {
            return $this->responseOk(new ProjectResource($request->project));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
