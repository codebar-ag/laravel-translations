<?php

namespace CodebarAG\LaravelTranslations;

use CodebarAG\LaravelTranslations\Commands\LaravelTranslationsCommand;
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
            ->hasMigration('create_laravel-translations_table')
            ->hasCommand(LaravelTranslationsCommand::class);
    }
}
