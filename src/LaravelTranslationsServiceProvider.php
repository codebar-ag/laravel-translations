<?php

namespace CodebarAg\LaravelTranslations;

use CodebarAg\LaravelTranslations\Commands\LaravelTranslationsFetchCommand;
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
            ->hasCommand(LaravelTranslationsFetchCommand::class);
    }
}
