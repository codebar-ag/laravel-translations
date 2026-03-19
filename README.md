# Laravel Translations

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebarag/laravel-translations.svg?style=flat-square)](https://packagist.org/packages/codebarag/laravel-translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/codebarag/laravel-translations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/codebarag/laravel-translations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/codebarag/laravel-translations/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/codebarag/laravel-translations/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/codebarag/laravel-translations.svg?style=flat-square)](https://packagist.org/packages/codebarag/laravel-translations)

`laravel-translations` scans your application source files for `__()` translation keys and creates JSON language files. It also includes a conversion command that can replace path-based keys with their resolved inline text.

## Requirements

| Package | PHP | Laravel |
|:--|:--|:--|
| current | 8.5.* | 13.x |

## Installation

You can install the package via composer:

```bash
composer require codebarag/laravel-translations
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-translations-config"
```

This is the contents of the published config file:

```php
return [
    'pattern' => '/__\(\s*([\'"])(?<key>(?:\\\\.|(?!\1).)*)\1/',
    'directories' => [
        'app',
        'resources',
    ],
];
```

`translations.directories` controls where the scanner looks for translation keys. Use relative paths from your Laravel project root.

## Commands

```bash
# Build lang/{locale}.json from __() usage
php artisan translations:fetch de

# Overwrite without confirmation
php artisan translations:fetch de --force

# Convert path-based keys to inline text in configured directories
php artisan translations:convert

# Preview conversion changes only
php artisan translations:convert --dry-run
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Rhys Lees](https://github.com/CodebarAG)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
