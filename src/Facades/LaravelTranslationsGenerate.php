<?php

namespace CodebarAg\LaravelTranslations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAg\LaravelTranslations\LaravelTranslationsFetch
 */
class LaravelTranslationsGenerate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CodebarAg\LaravelTranslations\LaravelTranslationsFetch::class;
    }
}
