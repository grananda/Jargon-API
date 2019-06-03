<?php

namespace App\Repositories;

use App\Models\Communications\Memo;
use App\Models\User;
use Illuminate\Database\Connection;

class MemoRepository extends CoreRepository
{
    /**
     * @var \App\Repositories\UserRepository
     */
    private $userRepository;

    /**
     * ProjectRepository constructor.
     *
     * @param \Illuminate\Database\Connection  $dbConnection
     * @param \App\Models\Communications\Memo  $model
     * @param \App\Repositories\UserRepository $userRepository
     */
    public function __construct(Connection $dbConnection, Memo $model, UserRepository $userRepository)
    {
        parent::__construct($dbConnection, $model);

        $this->userRepository = $userRepository;
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

    /**
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function createMemo(array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($attributes) {
            /** @var \App\Models\Communications\Memo $memo */
            $memo = $this->create($attributes);

            $this->addRecipients($memo, $attributes['recipients']);

            return $memo->fresh();
        });
    }

    /**
     * @param \App\Models\Communications\Memo $memo
     * @param array                           $attributes
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function updateMemo(Memo $memo, array $attributes)
    {
        return $this->dbConnection->transaction(function () use ($memo, $attributes) {
            /** @var \App\Models\Communications\Memo $memo */
            $memo = $this->update($memo, $attributes);

            $this->addRecipients($memo, $attributes['recipients']);

            return $memo->fresh();
        });
    }

    /**
     * Adds recipients to memo.
     *
     * @param \App\Models\Communications\Memo $entity
     * @param array                           $recipients
     *
     * @return \App\Models\Communications\Memo
     */
    private function addRecipients(Memo $entity, array $recipients)
    {
        $recipients = $this->userRepository->findAllWhereIn([
            'uuid' => $recipients,
        ]);

        $entity->setRecipients($recipients->pluck('id')->toArray());

        return $entity->refresh();
    }
}
