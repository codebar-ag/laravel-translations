<?php

namespace CodebarAg\LaravelTranslations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConvertTranslationPathsToInlineCommand extends Command
{
    protected $signature = 'translations:convert';

    protected $description = 'Command description';

    public function handle(): void
    {
        // Iterate all files in app directory and resources directory
        // If the file contains a translation path, convert it to inline translation

        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path(),
        ]);

        foreach (config('translations.directories') as $directory) {
            $this->replacePathTranslations(
                collect($disk->allFiles($directory))
            );
        }

        $this->info('Done!');
    }

    protected function replacePathTranslations(Collection $files): void
    {
        $files->each(function ($file) use (&$array) {
            $content = File::get($file);

            if (preg_match_all(config('translations.pattern'), $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $match = trim($match, "'");

                    // Filter out translations that are not paths
                    if (
                        Str::containsAll($match, ['.', '/']) &&
                        ! Str::contains($match, ' ')
                    ) {
                        $trans = trans($match) != $match ? trans($match) : null;

                        if ($trans) {
                            // Replace the translation path with the inline translation in the file
                            $content = str_replace(
                                "__('$match')",
                                "__('$trans')",
                                $content
                            );
                            File::put($file, $content);

                            $this->info("Found translation: $match");
                        } else {
                            $this->warn("No translation found for: $match in $file");
                        }
                    }
                }
            }
        });
    }
}
