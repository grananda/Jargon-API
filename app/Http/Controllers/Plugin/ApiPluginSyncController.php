<?php

namespace App\Http\Controllers\Plugin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Plugin\StoreApiPluginSyncRequest;
use App\Jobs\ProcessProjectRemoteSync;
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
            dispatch(new ProcessProjectRemoteSync($request->project, $request->json));

            return $this->responseOk([]);
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
