<?php

namespace CodebarAg\LaravelTranslations;

use CodebarAg\LaravelTranslations\Models\TranslationValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class LaravelTranslationsGenerate
{
    public Collection $translationKeys;

    public Collection $translationDB;

    public function handle(): void
    {
        $this->translationDB = TranslationValue::with('translation')->get();

        $files = collect();

        $this->translationDB->each(function ($translation) use (&$files) {
            // If file exists in the collection, add the translation to the array
            $locale = $translation->locale;

            if (! $files->has($locale)) {
                $files->put($locale, [
                    $translation->translation->key => $translation->value,
                ]);
            } else {
                $files->put(
                    $locale,
                    array_merge($files->get($locale), [
                        $translation->translation->key => $translation->value,
                    ])
                );
            }
        });

        $this->saveAsJson($files);
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
