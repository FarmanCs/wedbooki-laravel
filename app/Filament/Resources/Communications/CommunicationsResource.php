<?php

namespace App\Filament\Resources\Communications;

use App\Filament\Resources\Communications\Pages\CreateCommunications;
use App\Filament\Resources\Communications\Pages\EditCommunications;
use App\Filament\Resources\Communications\Pages\ListCommunications;
use App\Filament\Resources\Communications\Pages\ViewCommunications;
use App\Filament\Resources\Communications\Schemas\CommunicationsForm;
use App\Filament\Resources\Communications\Schemas\CommunicationsInfolist;
use App\Filament\Resources\Communications\Tables\CommunicationsTable;
use App\Models\Communications;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommunicationsResource extends Resource
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|BackedEnum|null $navigationTitle = 'Communications';

    protected static ?int $navigationSort=9;
    public static function form(Schema $schema): Schema
    {
        return CommunicationsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunicationsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunicationsTable::configure($table);
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
            'index' => ListCommunications::route('/'),
            'create' => CreateCommunications::route('/create'),
            'view' => ViewCommunications::route('/{record}'),
            'edit' => EditCommunications::route('/{record}/edit'),
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
