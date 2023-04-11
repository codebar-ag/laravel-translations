<?php

namespace CodebarAg\LaravelTranslations;

use CodebarAg\LaravelTranslations\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelTranslationsFetch
{
    public Collection $translationKeys;

    public Collection $translationDB;

    public function handle(): void
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
        // compare the translation keys with the translation db and if the key is not in the translation keys, delete it from the db
        $this->translationDB->each(function ($key) {
            if (! $this->translationKeys->has($key)) {
                Translation::where('key', $key)->delete();
            }
        });
    }
}
