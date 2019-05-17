<?php

namespace App\Events\Option;

use App\Models\Options\Option;

class OptionWasCreated
{
    /**
     * @var \App\Models\Options\Option
     */
    public $option;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Options\Option $option
     */
    public function __construct(Option $option)
    {
        $this->option = $option;
    }
}
