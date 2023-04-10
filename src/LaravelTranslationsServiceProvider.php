<?php

namespace CodebarAG\LaravelTranslations;

use CodebarAG\LaravelTranslations\Commands\LaravelTranslationsFetchCommand;
use CodebarAG\LaravelTranslations\Commands\LaravelTranslationsGenerateCommand;
use CodebarAG\LaravelTranslations\Nova\Translation;
use CodebarAG\LaravelTranslations\Nova\TranslationValue;
use Laravel\Nova\Nova;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelTranslationsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-translations')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations(
                'create_laravel-translations_table',
                'create_laravel-translation-values_table'
            )
            ->hasCommands(
                LaravelTranslationsFetchCommand::class,
                LaravelTranslationsGenerateCommand::class
            );
    }

    public function packageRegistered()
    {
        Nova::resources([
            Translation::class,
            TranslationValue::class,
        ]);
    }
}
