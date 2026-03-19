<?php

namespace CodebarAg\LaravelTranslations\Commands;

use CodebarAg\LaravelTranslations\Facades\LaravelTranslationsFetch;
use Illuminate\Console\Command;

class LaravelTranslationsFetchCommand extends Command
{
    public $signature = 'translations:fetch {locale : Locale code (for example: en, de, en_GB)} {--f|force : Overwrite existing locale file without confirmation}';

    public $description = 'Scan configured directories for __() keys and write lang/{locale}.json';

    public function handle(): int
    {
        $locale = (string) $this->argument('locale');

        if (! preg_match('/^[A-Za-z0-9_-]+$/', $locale)) {
            $this->error('Invalid locale. Use letters, numbers, underscores, or hyphens only.');

            return Command::FAILURE;
        }

        $directories = config('translations.directories', []);
        if (! is_array($directories) || $directories === []) {
            $this->error('No translation scan directories are configured. Set translations.directories in config/translations.php.');

            return Command::FAILURE;
        }

        if (file_exists(base_path("lang/{$locale}.json")) && ! $this->option('force')) {
            if (! $this->confirm('This will overwrite "lang/'.$locale.'.json". Continue?')) {
                $this->warn('Command aborted');

                return Command::FAILURE;
            }
        }

        $count = LaravelTranslationsFetch::handle($locale);

        $this->info("Wrote {$count} translations to lang/{$locale}.json.");

        return Command::SUCCESS;
    }
}
