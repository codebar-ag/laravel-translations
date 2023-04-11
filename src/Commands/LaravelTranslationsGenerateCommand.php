<?php

namespace CodebarAg\LaravelTranslations\Commands;

use CodebarAg\LaravelTranslations\Models\Translation;
use CodebarAg\LaravelTranslations\Models\TranslationValue;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LaravelTranslationsGenerateCommand extends Command
{
    public Collection $translationKeys;

    public Collection $translationDB;

    public $signature = 'translations:generate';

    public $description = 'Get Laravel Translations from the view files';

    public function handle(): int
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
        $this->saveAsPHP($files);

        $this->comment('All done');

        return self::SUCCESS;
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

    // WIP
    protected function saveAsPHP($files): void
    {
        $disk = Storage::build([
            'driver' => 'local',
            'root' => base_path('lang/testing/php/lang'),
        ]);

        // Create a file in the resources/lang folder with the filename of the locale and the content of the array
        $files->each(function ($file, $locale) use ($disk) {
            $content = '<?php'.PHP_EOL.PHP_EOL.'return '.var_export($file, true).';';

            // replace array ( with [
            $content = preg_replace('/array \(/', '[', $content);

            // replace ); with ];
            $content = preg_replace('/\);/', '];', $content);

            $disk->put("$locale.php", $content);
        });
    }
}
