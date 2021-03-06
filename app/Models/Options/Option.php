<?php

namespace App\Models\Options;

use App\Events\Option\OptionWasCreated;
use App\Events\Option\OptionWasDeleted;
use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends BaseEntity
{
    use HasUuid;

    const USER_OPTION = 'user';
    const APP_OPTION  = 'staff';

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
        'option_key',
        'option_value',
        'option_type',
        'option_category_id',
        'option_scope',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dispatchesEvents = [
        'created' => OptionWasCreated::class,
        'deleted' => OptionWasDeleted::class,
    ];

    /**
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(OptionCategory::class, 'option_category_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function userOptions()
    {
        return $this->hasMany(OptionUser::class, 'option_key', 'option_key');
    }

    /**
     * @return HasMany
     */
    public function appOptions()
    {
        return $this->hasMany(OptionApp::class, 'option_key', 'option_key');
    }
}
