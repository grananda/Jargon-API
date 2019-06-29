<?php

namespace App\Http\Controllers\Plugin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Plugin\StoreApiPluginSyncRequest;
use App\Http\Resources\JargonOptions\JargonOptionsResource;
use Exception;

class ApiPluginSyncController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Plugin\StoreApiPluginSyncRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreApiPluginSyncRequest $request)
    {
        try {
            return $this->responseOk(new JargonOptionsResource($request->project->jargonOptions));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
