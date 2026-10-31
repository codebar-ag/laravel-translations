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
            'links' => 'skip',
        ]);

        foreach (config('translations.directories', []) as $directory) {
            $this->scanForTranslations(
                collect($disk->allFiles($directory))
            );
        }

        $this->saveAsJson();

        return $this->translationKeys->count();
    }

    protected function scanForTranslations(Collection $files): void
    {
        $pattern = (string) config('translations.pattern', '/__\(\s*([\'"])(?<key>(?:\\\\.|(?!\1).)*)\1/');

        $files->each(function (string $file) use ($pattern) {
            $content = File::get(base_path($file));

            foreach ($this->matchTranslationKeys($pattern, $content) as $match) {
                if ($match === '') {
                    continue;
                }

                $this->translationKeys->put(stripslashes($match), stripslashes($match));
            }
        });
    }

    /**
     * @return array<int, string>
     */
    protected function matchTranslationKeys(string $pattern, string $content): array
    {
        if (preg_match_all($pattern, $content, $matches) !== false) {
            return $matches['key'] ?? [];
        }

        // The pattern ran into a PCRE limit (e.g. a very long unterminated string).
        // Retry line by line so one problematic line does not drop every key in the file.
        $keys = [];

        foreach (preg_split('/\R/', $content) ?: [] as $line) {
            if (preg_match_all($pattern, $line, $lineMatches)) {
                array_push($keys, ...($lineMatches['key'] ?? []));
            }
        }

        return $keys;
    }

    protected function saveAsJson(): void
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path('lang/'),
        ]);

        $disk->put(
            $this->locale.'.json',
            $this->translationKeys->sortKeys()->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
