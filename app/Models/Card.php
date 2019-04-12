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
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
