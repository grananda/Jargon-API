<?php


namespace Tests\traits;


use App\Models\Options\Option;
use App\Models\Options\OptionUser;
use App\Models\User;

trait CreateOptionUsers
{
    /**
     * Creates all user options for given user.
     *
     * @param \App\Models\User $user
     * @param array            $attributes
     *
     * @return mixed
     */
    public function createUserOptions(User $user, array $attributes = [])
    {
        /** @var \Illuminate\Database\Eloquent\Collection $options */
        $options = Option::where('option_scope', Option::USER_OPTION)->get();

        /** @var \App\Models\Options\Option $option */
        foreach ($options as $option) {
            factory(OptionUser::class)->create([
                'user_id'      => $user->id,
                'option_key'   => $option->option_key,
                'option_value' => $attributes[$option->option_key] ?? $option->option_value,
            ]);
        }

        return $user->fresh()->options;
    }
}