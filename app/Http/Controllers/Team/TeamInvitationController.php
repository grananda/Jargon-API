<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Team\UpdateTeamInvitationRequest;
use App\Repositories\TeamRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamInvitationController extends ApiController
{
    /** @var \App\Repositories\TeamRepository */
    protected $teamRepository;

    /**
     * TeamInvitationController constructor.
     *
     * @param \App\Repositories\TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * Accepts user invitation to organization.
     *
     * @param \App\Http\Requests\Team\UpdateTeamInvitationRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function update(UpdateTeamInvitationRequest $request)
    {
        try {
            /** @var \App\Models\Team $team */
            $team = $this->teamRepository->findOneByinvitationToken($request->invitationToken);

            $this->teamRepository->validateInvitation($team, $request->invitationToken);

            return $this->responseOk(true);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->responseNotFound($modelNotFoundException->getMessage());
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
