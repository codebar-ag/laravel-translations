<?php

use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

beforeEach(function () {
    File::ensureDirectoryExists(base_path('tmp/translations-fetch'));
    File::ensureDirectoryExists(base_path('lang'));
});

afterEach(function () {
    File::deleteDirectory(base_path('tmp/translations-fetch'));
    File::delete(base_path('lang/de.json'));
});

test('it fetches translation keys into locale json', function () {
    File::put(
        base_path('tmp/translations-fetch/example.php'),
        <<<'PHP'
<?php

__('Hello World');
__("auth.login");
__('validation.required', ['attribute' => 'email']);
PHP
    );

    config()->set('translations.directories', ['tmp/translations-fetch']);

    artisan('translations:fetch', [
        'locale' => 'de',
        '--force' => true,
    ])->assertSuccessful();

    $content = json_decode(File::get(base_path('lang/de.json')), true, 512, JSON_THROW_ON_ERROR);

    expect($content)->toBe([
        'Hello World' => 'Hello World',
        'auth.login' => 'auth.login',
        'validation.required' => 'validation.required',
    ]);
});

test('it rejects invalid locales', function () {
    config()->set('translations.directories', ['tmp/translations-fetch']);

    artisan('translations:fetch', [
        'locale' => 'de-DE!',
    ])->assertFailed();
});

test('it fails when no scan directories are configured', function () {
    config()->set('translations.directories', []);

    artisan('translations:fetch', [
        'locale' => 'de',
    ])->assertFailed();
});

test('it skips symbolic links in scanned directories', function () {
    File::ensureDirectoryExists(base_path('tmp/translations-fetch-outside'));
    File::put(base_path('tmp/translations-fetch-outside/linked.php'), "<?php __('Linked');");
    File::put(base_path('tmp/translations-fetch/example.php'), "<?php __('Hello World');");

    symlink(base_path('tmp/translations-fetch-outside/linked.php'), base_path('tmp/translations-fetch/linked.php'));
    symlink(base_path('tmp/translations-fetch-outside'), base_path('tmp/translations-fetch/linked-dir'));

    config()->set('translations.directories', ['tmp/translations-fetch']);

    try {
        artisan('translations:fetch', [
            'locale' => 'de',
            '--force' => true,
        ])->assertSuccessful();
    } finally {
        File::deleteDirectory(base_path('tmp/translations-fetch-outside'));
    }

    $content = json_decode(File::get(base_path('lang/de.json')), true, 512, JSON_THROW_ON_ERROR);

    expect($content)->toBe([
        'Hello World' => 'Hello World',
    ]);
});
