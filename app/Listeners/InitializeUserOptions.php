<?php

namespace App\Listeners;

use App\Events\User\UserWasActivated;
use App\Repositories\OptionUserRepository;

class InitializeUserOptions
{
    /**
     * @var \App\Repositories\OptionUserRepository
     */
    private $optionUserRepository;

    /**
     * InitializeUserOptions constructor.
     *
     * @param \App\Repositories\OptionUserRepository $optionUserRepository
     */
    public function __construct(OptionUserRepository $optionUserRepository)
    {
        $this->optionUserRepository = $optionUserRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserWasActivated $event
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function handle(UserWasActivated $event)
    {
        $this->optionUserRepository->rebuildUserOptions($event->user);
    }
}
