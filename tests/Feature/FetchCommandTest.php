<?php

use CodebarAg\LaravelTranslations\Commands\LaravelTranslationsFetchCommand;
use Illuminate\Support\Facades\File;

test('can fetch translations', function () {

    $fileName = 'translation.php';
    $file = "<?php __('Hello World');";

    File::put("$fileName", $file);

    \Pest\Laravel\artisan(LaravelTranslationsFetchCommand::class, [
        'locale' => 'de',
    ]);

})->group('translations');
