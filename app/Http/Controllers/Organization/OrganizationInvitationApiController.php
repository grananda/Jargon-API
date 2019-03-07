<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Organization\UpdateOrganizationInvitationRequest;
use App\Repositories\OrganizationRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrganizationInvitationApiController extends ApiController
{
    /** @var \App\Repositories\OrganizationRepository */
    protected $organizationRepository;

    /**
     * OrganizationUserController constructor.
     *
     * @param \App\Repositories\OrganizationRepository $organizationRepository
     */
    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * Accepts user invitation to organization.
     *
     * @param \App\Http\Requests\Organization\UpdateOrganizationInvitationRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function update(UpdateOrganizationInvitationRequest $request)
    {
        try {
            /** @var \App\Models\Organization $organization */
            $organization = $this->organizationRepository->findOneByinvitationToken($request->invitationToken);

            $this->organizationRepository->validateInvitation($organization);

            return $this->responseOk(true);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return $this->responseNotFound($modelNotFoundException->getMessage());
        } catch (Exception $e) {
            return $this->responseInternalError($e->getMessage());
        }
    }
}
