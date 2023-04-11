<?php

namespace CodebarAg\LaravelTranslations\Commands;

use CodebarAg\LaravelTranslations\Facades\LaravelTranslationsFetch;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class LaravelTranslationsFetchCommand extends Command
{
    public Collection $translationKeys;

    public Collection $translationDB;

    public $signature = 'translations:fetch';

    public $description = 'Get Laravel Translations from the view files';

    public function handle(): int
    {
        LaravelTranslationsFetch::handle();

        return true;
    }
}
