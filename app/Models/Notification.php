<?php

namespace App\Models;

class Notification extends BaseEntity
{
    const NOTIFICATION_EXPIRATION_THRESHOLD = 30;

    /**
     * Mark the notification as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * @return object
     */
    public function parseData()
    {
        return json_decode($this->data);
    }
}
