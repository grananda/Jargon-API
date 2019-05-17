<?php

namespace App\Models\Options;

use App\Models\BaseEntity;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionUser extends BaseEntity
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
        'option_key',
        'option_value',
        'user_id',
    ];

    /**
     * @return BelongsTo
     */
    protected function option()
    {
        return $this->belongsTo(Option::class, 'option_key', 'option_key');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function user()
    {
        return $this->belongsTo(User::class);
    }
}
