<?php

namespace CodebarAG\LaravelTranslations\Nova;

use CodebarAG\LaravelTranslations\Models\TranslationValue as TranslationValueModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class TranslationValue extends Resource
{
    public static $model = TranslationValueModel::class;

    public static $title = 'id';

    public static $search = [
        '',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Translation', 'translation', Translation::class)
                ->readonly(),

            Select::make('Locale', 'locale')
                ->filterable()
                ->options(config('translations.locales')),

            Text::make('Value', 'value'),
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
        return [];
    }
}
