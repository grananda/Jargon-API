<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public function responseOk($data, array $headers = [])
    {
        return response()->json(compact('data'), HttpResponse::HTTP_OK, $headers, JSON_PRETTY_PRINT);
    }

    /**
     * @param $data
     *
     * @return JsonResponse
     */
    public function responseCreated($data)
    {
        return response()->json(compact('data'), HttpResponse::HTTP_CREATED);
    }

    /**
     * @return JsonResponse
     */
    public function responseNoContent()
    {
        return response()->json(null, HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param $message
     *
     * @return JsonResponse
     */
    public function responseInternalError($message = null)
    {
        return response()->json(compact('message'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param $message
     *
     * @return JsonResponse
     */
    public function responseNotFound($message = null)
    {
        return response()->json(compact('message'), HttpResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param $message
     *
     * @return JsonResponse
     */
    public function responseValidationError($message = null)
    {
        return response()->json(compact('message'), HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
