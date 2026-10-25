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

test('it does not follow symbolic links', function () {
    Lang::addLines([
        'auth.login' => 'Sign in',
    ], 'en');

    File::ensureDirectoryExists(base_path('tmp/translations-convert-outside'));
    File::put(base_path('tmp/translations-convert-outside/linked.php'), "<?php __('auth.login');");
    File::put(base_path('tmp/translations-convert/example.php'), "<?php __('auth.login');");

    symlink(base_path('tmp/translations-convert-outside/linked.php'), base_path('tmp/translations-convert/linked.php'));

    config()->set('translations.directories', ['tmp/translations-convert']);

    try {
        artisan('translations:convert')->assertSuccessful();

        expect(File::get(base_path('tmp/translations-convert/example.php')))->toContain("__('Sign in');")
            ->and(File::get(base_path('tmp/translations-convert-outside/linked.php')))->toContain("__('auth.login');");
    } finally {
        File::deleteDirectory(base_path('tmp/translations-convert-outside'));
    }
});

test('it converts files containing long runs of backslashes in a string', function () {
    Lang::addLines([
        'auth.login' => 'Sign in',
    ], 'en');

    File::put(
        base_path('tmp/translations-convert/example.php'),
        "<?php\n\n__('auth.login');\n__('".str_repeat('\\', 40)."' . \$suffix);\n"
    );

    config()->set('translations.directories', ['tmp/translations-convert']);

    artisan('translations:convert')->assertSuccessful();

    expect(File::get(base_path('tmp/translations-convert/example.php')))
        ->toContain("__('Sign in');")
        ->not->toContain("__('auth.login');");
});

test('it keeps escaped quotes and arguments when converting', function () {
    Lang::addLines([
        'auth.welcome' => "It's :name",
    ], 'en');

    File::put(
        base_path('tmp/translations-convert/example.php'),
        "<?php\n\n__('auth.welcome', ['name' => \$name]);\n__('It\\'s.fine');\n"
    );

    config()->set('translations.directories', ['tmp/translations-convert']);

    artisan('translations:convert')->assertSuccessful();

    expect(File::get(base_path('tmp/translations-convert/example.php')))
        ->toContain("__('It\\'s :name', ['name' => \$name]);")
        ->toContain("__('It\\'s.fine');");
});
