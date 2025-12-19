<?php

namespace App\Filament\Resources\CMS;

use App\Filament\Resources\CMS\Pages\CreateCMS;
use App\Filament\Resources\CMS\Pages\EditCMS;
use App\Filament\Resources\CMS\Pages\ListCMS;
use App\Filament\Resources\CMS\Pages\ViewCMS;
use App\Filament\Resources\CMS\Schemas\CMSForm;
use App\Filament\Resources\CMS\Schemas\CMSInfolist;
use App\Filament\Resources\CMS\Tables\CMSTable;
use App\Models\CMS\cms_setting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CMSResource extends Resource
{
    protected static ?string $model = cms_setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CMSSetting';

    protected static ?string $navigationLabel = 'CMS Setting';

    public static function form(Schema $schema): Schema
    {
        return CMSForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CMSInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CMSTable::configure($table);
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
            'index' => ListCMS::route('/'),
            'create' => CreateCMS::route('/create'),
            'view' => ViewCMS::route('/{record}'),
            'edit' => EditCMS::route('/{record}/edit'),
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
