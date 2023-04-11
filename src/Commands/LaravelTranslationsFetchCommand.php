<?php

namespace CodebarAg\LaravelTranslations\Commands;

use CodebarAg\LaravelTranslations\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelTranslationsFetchCommand extends Command
{
    public Collection $translationKeys;

    public Collection $translationDB;

    public $signature = 'translations:fetch';

    public $description = 'Get Laravel Translations from the view files';

    public function handle(): int
    {
        $this->translationDB = Translation::all()->pluck('key');
        $this->translationKeys = collect();

        $disk = Storage::build([
            'driver' => 'local',
            'root' => config('translations.view_path'),
        ]);

        $this->scanForTranslations(
            collect($disk->allFiles())
        );
        $this->updateTranslations();
        $this->cleanOldTranslations();

        $this->info('Translation Keys: '.$this->translationKeys->count());
        $this->comment('All done');

        return self::SUCCESS;
    }

    protected function scanForTranslations(Collection $files): void
    {
        $files->each(function ($file) use (&$array) {

            $pattern = "/__\((.+?)\)/m";
            $content = File::get(base_path("resources/views/$file"));

            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $match = trim($match, "'");
                    if (! $this->translationKeys->has($match)) {
                        $this->translationKeys->put($match, [$file]);
                    } else {
                        $this->translationKeys->put(
                            $match,
                            array_merge($this->translationKeys->get($match), [$file])
                        );
                    }
                }
            }
        });
    }

    protected function updateTranslations(): void
    {
        $this->translationKeys->each(function ($files, $key) {
            Translation::withTrashed()->updateOrCreate([
                'key' => $key,
            ], [
                'files' => $files,
                'deleted_at' => null,
            ]);
        });
    }

    protected function cleanOldTranslations(): void
    {
        //Compare the keys from the database with the keys from the files
        ray($this->translationDB, $this->translationKeys, $this->translationDB->diff($this->translationKeys));
    }
}
