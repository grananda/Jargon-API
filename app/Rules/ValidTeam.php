<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class ValidTeam implements Rule
{
    /**
     * The User instance.
     *
     * @var \App\Models\User = null
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param \App\Models\User|null $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return (bool) $this->user->teams()
            ->where('is_owner', true)
            ->where('uuid', $value)
            ->first()
        ;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('Team does not belongs to user.');
    }
}
