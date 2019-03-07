<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Team\IndexTeamRequest;
use App\Http\Resources\Teams\TeamCollection;
use App\Repositories\TeamRepository;
use Exception;
use Illuminate\Http\Request;

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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
