<?php

namespace App\Models\Translations;

use App\Models\BaseEntity;
use App\Models\Dialect;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends BaseEntity
{
    use SoftDeletes;
    use HasUuid;

    protected $fillable = [
        'definition',
        'dialect_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function dialect()
    {
        return $this->belongsTo(Dialect::class);
    }

    /**
     * @param Node $key
     *
     * @return $this
     */
    public function setNode($key)
    {
        $this->node()->associate($key)->save();

        return $this;
    }

    /**
     * @param Dialect $dialect
     *
     * @return $this
     */
    public function setDialect($dialect)
    {
        $this->dialect()->associate($dialect)->save();

        return $this;
    }
}
