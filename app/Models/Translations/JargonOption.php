<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string translation_file_mode
 * @property string file_ext
 * @property string i18n_path
 */
class JargonOption extends Model
{
    protected $fillable = [
        'language',
        'file_ext',
        'framework',
        'i18n_path',
        'translation_file_mode',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Project to apply config to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
