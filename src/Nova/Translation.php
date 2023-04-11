<?php

namespace CodebarAG\LaravelTranslations\Nova;

use CodebarAG\LaravelTranslations\Models\Translation as TranslationModel;
use CodebarAG\LaravelTranslations\Nova\Actions\TranslationsFetch;
use CodebarAG\LaravelTranslations\Nova\Actions\TranslationsGenerate;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;

class Translation extends Resource
{
    public static $model = TranslationModel::class;

    public static $title = 'key';

    public static $search = [
        '',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Key', 'key')
                ->readonly(),

            Number::make('Files Count', function () {
                return count($this->files);
            }),

            Textarea::make('Files', function () {
                return implode(PHP_EOL, $this->files);
            })->alwaysShow(),

            HasMany::make('Translation Values', 'values', TranslationValue::class),
        ];
    }

    public function cards(Request $request): array
    {
        return [];
    }

    public function filters(Request $request): array
    {
        return [];
    }

    public function lenses(Request $request): array
    {
        return [];
    }

    public function actions(Request $request): array
    {
        return [
            (new TranslationsFetch)->standalone(),
            (new TranslationsGenerate)->standalone(),
        ];
    }
}
