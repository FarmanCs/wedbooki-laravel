<?php

namespace App\Filament\Resources\Settings\Resources\Generals;

use App\Filament\Resources\Settings\Resources\Generals\Pages\CreateGeneral;
use App\Filament\Resources\Settings\Resources\Generals\Pages\EditGeneral;
use App\Filament\Resources\Settings\Resources\Generals\Pages\ViewGeneral;
use App\Filament\Resources\Settings\Resources\Generals\Schemas\GeneralForm;
use App\Filament\Resources\Settings\Resources\Generals\Schemas\GeneralInfolist;
use App\Filament\Resources\Settings\Resources\Generals\Tables\GeneralsTable;
use App\Filament\Resources\Settings\SettingsResource;
use App\Models\Cms\cms_setting;
use App\Models\General;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GeneralResource extends Resource
{
    protected static ?string $model = cms_setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SettingsResource::class;

    protected static ?string $recordTitleAttribute = 'General';

    protected static ?string $navigationLabel = 'General';

    public static function form(Schema $schema): Schema
    {
        return GeneralForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GeneralInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeneralsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateGeneral::route('/create'),
            'view' => ViewGeneral::route('/{record}'),
            'edit' => EditGeneral::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
