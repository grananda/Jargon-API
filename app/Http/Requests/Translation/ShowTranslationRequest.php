<?php

namespace App\Http\Requests\Translation;

use App\Http\Requests\Request;
use App\Models\Translations\Translation;

class ShowTranslationRequest extends Request
{
    /**
     * The Translation instance.
     *
     * @var \App\Models\Translations\Translation
     */
    public $translation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->translation = Translation::findByUuidOrFail($this->route('id'));

        return $this->user()->can('show', [Translation::class, $this->translation->node->project]);
    }
}
