<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends BaseEntity
{
    use SoftDeletes;

    const ITEM_TOKEN_LENGTH = 50;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'price',
        'invoice_date',
        'invoice_status',
    ];

    protected $hidden = [
        'item_token',
    ];
}
