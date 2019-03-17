<?php

namespace App\Repositories\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait InvitationTrait
{
    /**
     * Returns organization related to user validation token.
     *
     * @param string $token
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function findOneByInvitationToken(string $token)
    {
        return $this->getModel()
            ->with('collaborators')
            ->whereHas('nonActiveMembers', function ($query) use ($token) {
                $expDate = Carbon::now()->subDays(env('INVITATION_EXPIRATION_DAYS'));

                $query->where('validation_token', $token);
                $query->whereDate('collaborators.created_at', '>=', $expDate);
            })->firstOrFail();
    }

    /**
     * Validate user invitation.
     *
     * @param \Illuminate\Database\Eloquent\Model $entity
     */
    public function validateInvitation(Model $entity)
    {
        $entity->validateMember($entity->collaborators()->first());
    }
}
