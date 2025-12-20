<?php

namespace App\Filament\Resources\RegionManagments;

use App\Filament\Resources\RegionManagments\Pages\CreateRegionManagment;
use App\Filament\Resources\RegionManagments\Pages\EditRegionManagment;
use App\Filament\Resources\RegionManagments\Pages\ListRegionManagments;
use App\Filament\Resources\RegionManagments\Pages\ViewRegionManagment;
use App\Filament\Resources\RegionManagments\Schemas\RegionManagmentForm;
use App\Filament\Resources\RegionManagments\Schemas\RegionManagmentInfolist;
use App\Filament\Resources\RegionManagments\Tables\RegionManagmentsTable;
use App\Models\RegionManagment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class RegionManagmentResource extends Resource
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;


    protected static string|null $title = 'Regions';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return RegionManagmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RegionManagmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegionManagmentsTable::configure($table);
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
            'index' => ListRegionManagments::route('/'),
            'create' => CreateRegionManagment::route('/create'),
            'view' => ViewRegionManagment::route('/{record}'),
            'edit' => EditRegionManagment::route('/{record}/edit'),
        ];
    }
}
