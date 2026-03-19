<?php

namespace CodebarAg\LaravelTranslations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertTranslationPathsToInlineCommand extends Command
{
    protected $signature = 'translations:convert {--d|dry-run : Show potential changes without writing files}';

    protected $description = 'Replace translation path keys in __() calls with resolved inline text';

    public function handle(): int
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path(),
        ]);

        $directories = config('translations.directories', []);
        if (! is_array($directories) || $directories === []) {
            $this->error('No translation scan directories are configured. Set translations.directories in config/translations.php.');

            return Command::FAILURE;
        }

        $updatedFiles = 0;
        foreach ($directories as $directory) {
            $this->replacePathTranslations(
                collect($disk->allFiles($directory)),
                $updatedFiles
            );
        }

        $mode = $this->option('dry-run') ? 'Dry run completed' : 'Done';
        $this->info("{$mode}. Updated {$updatedFiles} files.");

        return Command::SUCCESS;
    }

    protected function replacePathTranslations(Collection $files, int &$updatedFiles): void
    {
        $files->each(function (string $file) use (&$updatedFiles) {
            $absolutePath = base_path($file);
            $content = File::get($absolutePath);
            $didChange = false;

            $updatedContent = preg_replace_callback(
                '/__\(\s*([\'"])(?<key>(?:\\\\.|(?!\1).)*)\1(?<rest>\s*(?:,\s*[^)]*)?)\)/',
                function (array $match) use ($file, &$didChange): string {
                    $key = stripslashes($match['key']);
                    if (! $this->isLikelyTranslationPath($key)) {
                        return $match[0];
                    }

                    $translation = trans($key);
                    if ($translation === $key) {
                        $this->warn("No translation found for: {$key} in {$file}");

                        return $match[0];
                    }

                    $didChange = true;
                    $quote = $match[1];
                    $escapedTranslation = str_replace($quote, '\\'.$quote, $translation);
                    $rest = $match['rest'];
                    $this->line("Converted translation key: {$key} in {$file}");

                    return "__({$quote}{$escapedTranslation}{$quote}{$rest})";
                },
                $content
            );

            if ($didChange && is_string($updatedContent)) {
                $updatedFiles++;

                if (! $this->option('dry-run')) {
                    File::put($absolutePath, $updatedContent);
                }
            }
        });
    }

    protected function isLikelyTranslationPath(string $value): bool
    {
        return Str::contains($value, '.') && ! Str::contains($value, ' ');
    }
}
