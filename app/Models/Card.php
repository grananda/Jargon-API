<?php

namespace App\Models;

class Card extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'stripe_id',
        'brand',
        'country',
        'last4',
        'exp_month',
        'exp_year',
        'address_city',
        'address_country',
        'address_line1',
        'address_line2',
        'address_state',
        'address_zip',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
