<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

use function Pest\Laravel\artisan;

beforeEach(function () {
    File::ensureDirectoryExists(base_path('tmp/translations-convert'));
});

afterEach(function () {
    File::deleteDirectory(base_path('tmp/translations-convert'));
});

test('it converts translation path keys to inline text', function () {
    Lang::addLines([
        'auth.login' => 'Sign in',
    ], 'en');

    File::put(
        base_path('tmp/translations-convert/example.php'),
        <<<'PHP'
<?php

__('auth.login');
PHP
    );

    config()->set('translations.directories', ['tmp/translations-convert']);

    artisan('translations:convert')->assertSuccessful();

    expect(File::get(base_path('tmp/translations-convert/example.php')))
        ->toContain("__('Sign in');")
        ->not->toContain("__('auth.login');");
});

test('it does not modify files during dry run', function () {
    Lang::addLines([
        'auth.login' => 'Sign in',
    ], 'en');

    File::put(
        base_path('tmp/translations-convert/example.php'),
        <<<'PHP'
<?php

__('auth.login');
PHP
    );

    config()->set('translations.directories', ['tmp/translations-convert']);

    artisan('translations:convert', [
        '--dry-run' => true,
    ])->assertSuccessful();

    expect(File::get(base_path('tmp/translations-convert/example.php')))
        ->toContain("__('auth.login');")
        ->not->toContain("__('Sign in');");
});

test('it fails when directories are not configured', function () {
    config()->set('translations.directories', []);

    artisan('translations:convert')->assertFailed();
});
