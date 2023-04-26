<?php

use CodebarAg\LaravelTranslations\Commands\LaravelTranslationsFetchCommand;
use Illuminate\Support\Facades\File;
use function Pest\Laravel\artisan;

test('can fetch translations', function () {

    $fileName = 'translation.php';
    $file = "<?php __('Hello World');";

    File::put("$fileName", $file);

    artisan(LaravelTranslationsFetchCommand::class, [
        'locale' => 'de',
    ]);

})->group('translations')->to;
