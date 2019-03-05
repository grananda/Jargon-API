<?php

namespace App\Models\Options;

use App\Models\BaseEntity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionApp extends BaseEntity
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
    ];

    /**
     * @return BelongsTo
     */
    protected function option()
    {
        return $this->belongsTo(Option::class, 'option_key', 'option_key');
    }
}
