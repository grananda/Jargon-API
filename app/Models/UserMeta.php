<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMeta extends BaseEntity
{
    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'billing_information',
        'user_id',
        'vat_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getBillingInformation()
    {
        return htmlspecialchars($this->billing_information);
    }
}
