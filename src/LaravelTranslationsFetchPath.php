<?php

namespace CodebarAg\LaravelTranslations;

use CodebarAg\LaravelTranslations\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelTranslationsFetchPath
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

        #$this->updateTranslations();

        ray($this->translationKeys);

//        $this->cleanOldTranslations();
    }

    protected function scanForTranslations(Collection $files): void
    {
        $files->each(function ($file) use (&$array) {

            $pattern = "/__\((.+?)\)/m";
            $content = File::get(base_path("resources/views/$file"));

            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $match = trim($match, "'");
                    if (!$this->translationKeys->has($match)) {
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

    protected function exportTranslations(): void
    {

    }

    protected function updateTranslations(): void
    {
        $this->translationKeys->each(function ($files, $key) {
            $translation = Translation::withTrashed()->updateOrCreate([
                'key' => $key,
            ], [
                'files' => $files,
                'deleted_at' => null,
            ]);

            if ($translation->wasRecentlyCreated) {
                $translation->values()->createMany([
                    'locale' => config('translations.default_locale'),
                    'value' => $key
                ]);
            }
        });
    }

    protected function cleanOldTranslations(): void
    {
        // compare the translation keys with the translation db and if the key is not in the translation keys, delete it from the db
        $this->translationDB->each(function ($key) {
            if (!$this->translationKeys->has($key)) {
                Translation::where('key', $key)->delete();
            }
        });
    }

    protected function saveAsJson($files): void
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path('lang/testing/json/lang'),
        ]);

        // Create a file in the resources/lang folder with the filename of the locale and the content of the array
        $files->each(function ($file, $locale) use ($disk) {
            $disk->put("$locale.json", json_encode($file, JSON_PRETTY_PRINT));
        });
    }
}
