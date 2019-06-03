<?php

namespace App\Policies;

use App\Models\Communications\Memo;
use App\Models\User;

class MemoPolicy extends AbstractPolicy
{
    /**
     * Determines is a user can list projects.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function list(User $user)
    {
        return true;
    }

    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function staffList(User $user)
    {
        return $user->checkUserHasRoleType('staff') && ! $user->checkUserHasRole('junior-staff');
    }

    /**
     * @param \App\Models\User                $user
     * @param \App\Models\Communications\Memo $memo
     *
     * @return bool
     */
    public function show(User $user, Memo $memo)
    {
        return $memo->status === 'sent' && $memo->recipients()->find($user->id);
    }

    /**
     * @param \App\Models\User                $user
     * @param \App\Models\Communications\Memo $memo
     *
     * @return bool
     */
    public function staffShow(User $user, Memo $memo)
    {
        return $user->checkUserHasRoleType('staff') && ! $user->checkUserHasRole('junior-staff');
    }

    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function staffStore(User $user)
    {
        return $user->checkUserHasRoleType('staff') && ! $user->checkUserHasRole('junior-staff');
    }

    /**
     * @param \App\Models\User                $user
     * @param \App\Models\Communications\Memo $memo
     *
     * @return bool
     */
    public function update(User $user, Memo $memo)
    {
        return $memo->status === 'sent' && $memo->recipients()->find($user->id);
    }

    /**
     * @param \App\Models\User                $user
     * @param \App\Models\Communications\Memo $memo
     *
     * @return bool
     */
    public function staffUpdate(User $user, Memo $memo)
    {
        return $memo->status === 'draft' && $user->checkUserHasRoleType('staff') && ! $user->checkUserHasRole('junior-staff');
    }

    /**
     * @param \App\Models\User                $user
     * @param \App\Models\Communications\Memo $memo
     *
     * @return bool
     */
    public function delete(User $user, Memo $memo)
    {
        return $memo->status === 'sent' && $memo->recipients()->find($user->id);
    }

    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function staffDelete(User $user)
    {
        return $user->checkUserHasRoleType('staff') && ! $user->checkUserHasRole('junior-staff');
    }
}
