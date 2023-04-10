<?php

namespace CodebarAG\LaravelTranslations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslationValue extends Model
{
    use SoftDeletes;

    protected $table = 'laravel_translation_values';

    protected $fillable = [
        'lang',
        'value',
    ];

    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class);
    }
}
