<?php

namespace App\Listeners;

use App\Events\Option\OptionWasDeleted;
use App\Models\Options\Option;
use App\Models\Role;
use App\Models\User;
use App\Repositories\OptionAppRepository;
use App\Repositories\OptionUserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteOptionUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\OptionUserRepository
     */
    private $optionUserRepository;

    /**
     * @var \App\Repositories\OptionAppRepository
     */
    private $optionAppRepository;

    /**
     * DeleteOptionUser constructor.
     *
     * @param \App\Repositories\OptionUserRepository $optionUserRepository
     * @param \App\Repositories\OptionAppRepository  $optionAppRepository
     */
    public function __construct(OptionUserRepository $optionUserRepository, OptionAppRepository $optionAppRepository)
    {
        $this->optionUserRepository = $optionUserRepository;
        $this->optionAppRepository  = $optionAppRepository;
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\Option\OptionWasDeleted $event
     *
     * @throws \Throwable
     *
     * @return void
     */
    public function handle(OptionWasDeleted $event)
    {
        /** @var \App\Models\Options\Option $option */
        $option = $event->option;

        if ($option->option_scope === Option::USER_OPTION) {
            /** @var \Illuminate\Database\Query\Builder $query */
            $query = User::query()
                ->whereHas('roles', function ($q) {
                    $q->whereIn('roles.role_type', [Role::ROLE_USER_TYPE]);
                })
            ;

            $query->chunk(100, function ($users) use ($option) {
                /** @var \App\Models\User $user */
                foreach ($users as $user) {
                    /** @var \App\Models\Options\OptionUser $optionUser */
                    $optionUser = $user->options()->where('option_key', $option->option_key)->first();

                    if ($optionUser) {
                        $this->optionUserRepository->delete($optionUser);
                    }
                }
            });
        } elseif ($option->option_scope === Option::APP_OPTION) {
            $option = $this->optionAppRepository->findBy(['option_key' => $option->option_key]);

            $this->optionAppRepository->delete($option);
        }
    }
}
