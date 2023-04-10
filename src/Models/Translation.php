<?php

namespace CodebarAG\LaravelTranslations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use SoftDeletes;

    protected $table = 'laravel_translations';

    protected $fillable = [
        'key',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class);
    }
}
