<?php


use CodebarAg\LaravelTranslations\Commands\LaravelTranslationsFetchCommand;
use Illuminate\Support\Facades\Storage;

test('can fetch translations', function () {
    Storage::fake();

    \Pest\Laravel\artisan(LaravelTranslationsFetchCommand::class, ['locale' => 'test'])
        ->expectsOutput('2 translations.')
        ->assertExitCode(0);

    $disk = Storage::build([
        'driver' => 'local',
        'root' => base_path('lang/'),
    ]);

    $disk->assertExists('test.json');
});
