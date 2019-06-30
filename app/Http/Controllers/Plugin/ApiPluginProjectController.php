<?php

namespace App\Http\Controllers\Plugin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Plugin\IndexApiPluginProjectRequest;
use App\Http\Resources\JargonOptions\JargonOptionsResource;
use Exception;

class ApiPluginProjectController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\Plugin\IndexApiPluginProjectRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexApiPluginProjectRequest $request)
    {
        try {
            return $this->responseOk(new JargonOptionsResource($request->project->jargonOptions));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
