<?php

namespace App\Repositories;

use App\Models\Communications\Memo;
use App\Models\User;
use Illuminate\Database\Connection;

class MemoRepository extends CoreRepository
{
    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection $dbConnection
     * @param \App\Models\Communications\Memo $model
     */
    public function __construct(Connection $dbConnection, Memo $model)
    {
        parent::__construct($dbConnection, $model);
    }

    /**
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUserMemos(User $user)
    {
        return $this->getQuery()
            ->where('status', 'sent')
            ->whereHas('recipients', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();
    }

    /**
     * @param \App\Models\Communications\Memo $memo
     * @param \App\Models\User                $recipient
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function deleteRecipient(Memo $memo, User $recipient)
    {
        return $this->dbConnection->transaction(function () use ($memo, $recipient) {
            $memo->recipients()->detach($recipient->id);

            return $memo->fresh();
        });
    }

    /**
     * @param \App\Models\Communications\Memo $memo
     * @param \App\Models\User                $recipient
     * @param bool                            $isRead
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function setRead(Memo $memo, User $recipient, bool $isRead = true)
    {
        return $this->dbConnection->transaction(function () use ($memo, $recipient, $isRead) {
            $memo->setIsRead($recipient, $isRead);

            return $memo->fresh();
        });
    }
}
