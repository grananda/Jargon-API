<?php

namespace App\Models\Options;

use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionCategory extends BaseEntity
{
    use HasUuid;

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
        'title',
        'description',
    ];

    /**
     * @return HasMany
     */
    public function options()
    {
        return $this->hasMany(Option::class, 'option_category_id', 'id');
    }
}
