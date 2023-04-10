<?php

namespace CodebarAG\LaravelTranslations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAG\LaravelTranslations\LaravelTranslations
 */
class LaravelTranslations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CodebarAG\LaravelTranslations\LaravelTranslations::class;
    }
}
