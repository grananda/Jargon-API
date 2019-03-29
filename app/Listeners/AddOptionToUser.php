<?php

namespace App\Listeners;

use App\Events\Option\OptionWasCreated;
use App\Models\Options\Option;
use App\Models\Role;
use App\Models\User;
use App\Repositories\OptionUserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddOptionToUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\OptionUserRepository
     */
    private $optionUserRepository;

    /**
     * AddOptionToUser constructor.
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
     * @param \App\Events\Option\OptionWasCreated $event
     *
     * @return void
     */
    public function handle(OptionWasCreated $event)
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
                    $this->optionUserRepository->createUserOption($user, [
                        'option_value' => $option->option_value,
                        'option_key'   => $option->option_key,
                    ]);
                }
            });
        }
    }
}
