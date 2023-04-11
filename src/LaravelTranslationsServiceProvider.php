<?php

namespace CodebarAg\LaravelTranslations;

use CodebarAg\LaravelTranslations\Commands\LaravelTranslationsFetchCommand;
use CodebarAg\LaravelTranslations\Commands\LaravelTranslationsGenerateCommand;
use CodebarAg\LaravelTranslations\Nova\Translation;
use CodebarAg\LaravelTranslations\Nova\TranslationValue;
use CodebarAg\LaravelTranslations\Policies\Nova\TranslationPolicy;
use CodebarAg\LaravelTranslations\Policies\Nova\TranslationValuePolicy;
use Illuminate\Support\Facades\Gate;
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

        Nova::serving(function () {
            Gate::policy(Models\Translation::class, TranslationPolicy::class);
            Gate::policy(Models\TranslationValue::class, TranslationValuePolicy::class);
        });
    }
}
