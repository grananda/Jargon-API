<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Project\DeleteProjectRequest;
use App\Http\Requests\Project\IndexProjectRequest;
use App\Http\Requests\Project\ShowProjectRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\Projects\Project as ProjectResource;
use App\Http\Resources\Projects\ProjectCollection;
use App\Repositories\ProjectRepository;
use Exception;

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
     * @param \App\Http\Requests\Project\StoreProjectRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            $team = $this->projectRepository->createProject($request->user(), $request->validated());

            return $this->responseCreated(new ProjectResource($team));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
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
     * @param \App\Http\Requests\Project\UpdateProjectRequest $request
     * @param int                                             $id
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        try {
            $organization = $this->projectRepository->updateTeam($request->project, $request->validated());

            return $this->responseOk(new ProjectResource($organization));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Project\DeleteProjectRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteProjectRequest $request)
    {
        try {
            $this->projectRepository->delete($request->project);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
