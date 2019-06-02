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

    public function show(User $user, Memo $memo)
    {
        return $memo->status === 'sent' && $memo->recipients()->find($user->id);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Communications\Memo $memo
     * @return bool
     */
    public function delete(User $user, Memo $memo)
    {
        return $memo->status === 'sent' && $memo->recipients()->find($user->id);
    }
}
