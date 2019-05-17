<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Organization\DeleteOrganizationRequest;
use App\Http\Requests\Organization\IndexOrganizationRequest;
use App\Http\Requests\Organization\ShowOrganizationRequest;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Http\Resources\Organizations\Organization as OrganizationResource;
use App\Http\Resources\Organizations\OrganizationCollection;
use App\Repositories\OrganizationRepository;
use Exception;
use Illuminate\Http\JsonResponse;

class OrganizationApiController extends ApiController
{
    /**
     * The OrganizationRepository instance.
     *
     * @var \App\Repositories\OrganizationRepository
     */
    protected $organizationRepository;

    /**
     * OrganizationApiController constructor.
     *
     * @param \App\Repositories\OrganizationRepository $organizationRepository
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @param \App\Http\Requests\Organization\IndexOrganizationRequest $request
     *
     * @return JsonResponse
     */
    public function index(IndexOrganizationRequest $request)
    {
        try {
            $organizations = $this->organizationRepository->findAllByMember($request->user());

            return $this->responseOk(new OrganizationCollection($organizations));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Single single organization.
     *
     * @param \App\Http\Requests\Organization\ShowOrganizationRequest $request
     *
     * @return JsonResponse
     */
    public function show(ShowOrganizationRequest $request)
    {
        try {
            return $this->responseOk(new OrganizationResource($request->organization));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Creates a new organization.
     *
     * @param \App\Http\Requests\Organization\StoreOrganizationRequest $request
     *
     * @throws \Throwable
     *
     * @return JsonResponse
     */
    public function store(StoreOrganizationRequest $request)
    {
        try {
            $organization = $this->organizationRepository->createOrganization($request->user(), $request->validated());

            return $this->responseCreated(new OrganizationResource($organization));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Updates organization.
     *
     * @param \App\Http\Requests\Organization\UpdateOrganizationRequest $request
     *
     * @throws \Throwable
     *
     * @return JsonResponse
     */
    public function update(UpdateOrganizationRequest $request)
    {
        try {
            $organization = $this->organizationRepository->updateOrganization($request->organization, $request->validated());

            return $this->responseOk(new OrganizationResource($organization));
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }

    /**
     * Deletes an organization.
     *
     * @param \App\Http\Requests\Organization\DeleteOrganizationRequest $request
     *
     * @throws \Throwable
     *
     * @return JsonResponse
     */
    public function destroy(DeleteOrganizationRequest $request)
    {
        try {
            $this->organizationRepository->delete($request->organization);

            return $this->responseNoContent();
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
