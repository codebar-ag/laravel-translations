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

        $this->saveAsJson();
    }

    protected function scanForTranslations(Collection $files): void
    {
        $files->each(function ($file) use (&$array) {
            $pattern = "/__\((.+?)\)/m";
            $content = File::get(base_path("resources/views/$file"));

            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    $match = trim($match, "'");
                    $this->translationKeys->put($match, $match);
                }
            }
        });
    }

    protected function saveAsJson(): void
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path('lang/testing/json/lang'),
        ]);

        $disk->put(config('translations.default_locale').'.json', $this->translationKeys->sortKeys()->toJson(JSON_PRETTY_PRINT));
    }
}
