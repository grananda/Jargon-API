<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Project\UpdateProjectInvitationRequest;
use App\Repositories\ProjectRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProjectInvitationController extends ApiController
{
    /** @var \App\Repositories\ProjectRepository */
    protected $projectRepository;

    /**
     * ProjectInvitationController constructor.
     *
     * @param \App\Repositories\ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Project\UpdateProjectInvitationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectInvitationRequest $request)
    {
        try {
            /** @var \App\Models\Translations\Project $project */
            $project = $this->projectRepository->findOneByinvitationToken($request->invitationToken);

            $this->projectRepository->validateInvitation($project, $request->invitationToken);

            return $this->responseOk(true);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->responseNotFound($modelNotFoundException->getMessage());
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
