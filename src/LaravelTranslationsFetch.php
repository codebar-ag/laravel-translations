<?php

namespace CodebarAg\LaravelTranslations;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelTranslationsFetch
{
    public Collection $translationKeys;

    public string $locale;

    public function __construct()
    {
        $this->translationKeys = collect();
    }

    public function handle(string $locale): int
    {
        $this->locale = $locale;

        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path(),
        ]);

        foreach (config('translations.directories') as $directory) {
            $this->scanForTranslations(
                collect($disk->allFiles($directory))
            );
        }

        $this->saveAsJson();

        return $this->translationKeys->count();
    }

    protected function scanForTranslations(Collection $files): void
    {
        $files->each(function ($file) use (&$array) {
            $content = File::get($file);

            if (preg_match_all(config('translations.pattern'), $content, $matches)) {
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
            'root' => base_path('lang/'),
        ]);

        $disk->put(
            $this->locale.'.json',
            $this->translationKeys->sortKeys()->toJson(JSON_PRETTY_PRINT)
        );
    }
}
