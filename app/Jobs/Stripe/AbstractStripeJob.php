<?php

namespace App\Jobs\Stripe;

use App\Jobs\AbstractJob;

class AbstractStripeJob extends AbstractJob
{
    /**
     * The Stripe Event data.
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param array $data
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['stripe', 'stripe:'.$this->data['customer']];
    }
}
