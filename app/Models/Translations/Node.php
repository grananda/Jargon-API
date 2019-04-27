<?php

namespace App\Models\Translations;

use App\Models\BaseEntity;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Node extends BaseEntity
{
    use HasUuid;
    use NodeTrait;

    const TEMPLATE_KEY   = 'translation_node.api.attach.template_node_key';
    const NEW_NODE_INDEX = 100000;

    protected $fillable = [
        'key',
        'route',
        'sort_index',
        'project_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Copies a single node.
     *
     * @return Model
     */
    public function copy(): Model
    {
        $new = $this->replicate();
        $new->push();

        return $new;
    }
}
