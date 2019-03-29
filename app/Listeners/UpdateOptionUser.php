<?php

namespace App\Listeners;

use App\Events\Option\OptionWasUpdated;
use App\Models\Options\Option;
use App\Models\Role;
use App\Models\User;
use App\Repositories\OptionUserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOptionUser implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var \App\Repositories\OptionUserRepository
     */
    private $optionUserRepository;

    /**
     * AddOptionUser constructor.
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
     * @param \App\Events\Option\OptionWasUpdated $event
     *
     * @return void
     */
    public function handle(OptionWasUpdated $event)
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
                        $this->optionUserRepository->update($optionUser, [
                            'option_value' => $option->option_value,
                        ]);
                    }
                }
            });
        }
    }
}
