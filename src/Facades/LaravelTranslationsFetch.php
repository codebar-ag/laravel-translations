<?php

namespace CodebarAG\LaravelTranslations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAG\LaravelTranslations\LaravelTranslationsFetch
 */
class LaravelTranslationsFetch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CodebarAG\LaravelTranslations\LaravelTranslationsFetch::class;
    }
}