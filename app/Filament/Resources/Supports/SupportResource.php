<?php

namespace App\Filament\Resources\Supports;

use App\Filament\Resources\Supports\Pages\CreateSupport;
use App\Filament\Resources\Supports\Pages\EditSupport;
use App\Filament\Resources\Supports\Pages\ListSupports;
use App\Filament\Resources\Supports\Pages\ViewSupport;
use App\Filament\Resources\Supports\Schemas\SupportForm;
use App\Filament\Resources\Supports\Schemas\SupportInfolist;
use App\Filament\Resources\Supports\Tables\SupportsTable;
use App\Models\Cms\CmsSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupportResource extends Resource
{
//    protected static ?string $model = CmsSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|BackedEnum|null $navigationTitle = 'Support';

    protected static int|null $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return SupportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SupportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportsTable::configure($table);
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
            'index' => ListSupports::route('/'),
            'create' => CreateSupport::route('/create'),
            'view' => ViewSupport::route('/{record}'),
            'edit' => EditSupport::route('/{record}/edit'),
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
