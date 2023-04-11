<?php

namespace CodebarAg\LaravelTranslations\Commands;

use CodebarAg\LaravelTranslations\Facades\LaravelTranslationsFetch;
use Illuminate\Console\Command;

class LaravelTranslationsFetchCommand extends Command
{
    public $signature = 'translations:fetch {locale} {--f|force}';

    public $description = 'Get Laravel Translations from the view files';

    public function handle(): int
    {
        //check if locale.json exists allow --force
        if(file_exists(base_path("lang/{$this->argument('locale')}.json")) && !$this->option('force')) {
            if (! $this->confirm('This will overwrite "lang/'. $this->argument('locale'). '.json" Do you wish to continue?')) {
                $this->warn('Command aborted');
                return Command::FAILURE;
            }
        }

        //test command


        $count = LaravelTranslationsFetch::handle($this->argument('locale'));

        $this->info("{$count} translations.");

        return Command::SUCCESS;
    }
}
