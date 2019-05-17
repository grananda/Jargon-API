<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Team\DeleteTeamRequest;
use App\Http\Requests\Team\IndexTeamRequest;
use App\Http\Requests\Team\ShowTeamRequest;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\Teams\Team as TeamResource;
use App\Http\Resources\Teams\TeamCollection;
use App\Repositories\TeamRepository;
use Exception;

class TeamController extends ApiController
{
    /**
     * The TeamRepository instance.
     *
     * @var \App\Repositories\TeamRepository
     */
    private $teamRepository;

    /**
     * TeamController constructor.
     *
     * @param \App\Repositories\TeamRepository $teamRepository
     */
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Team\IndexTeamRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexTeamRequest $request)
    {
        try {
            $teams = $this->teamRepository->findAllBymember($request->user());

            return $this->responseOk(new TeamCollection($teams));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Team\StoreTeamRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTeamRequest $request)
    {
        try {
            $team = $this->teamRepository->createTeam($request->user(), $request->validated());

            return $this->responseCreated(new TeamResource($team));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Team\ShowTeamRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowTeamRequest $request)
    {
        try {
            return $this->responseOk(new TeamResource($request->team));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Team\UpdateTeamRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTeamRequest $request)
    {
        try {
            $organization = $this->teamRepository->updateTeam($request->team, $request->validated());

            return $this->responseOk(new TeamResource($organization));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Team\DeleteTeamRequest $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteTeamRequest $request)
    {
        try {
            $this->teamRepository->delete($request->team);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
