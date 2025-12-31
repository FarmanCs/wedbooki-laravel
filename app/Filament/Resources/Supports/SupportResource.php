<?php

namespace App\Filament\Resources\Supports;

use App\Filament\Resources\Supports\Pages\ListSupports;
use App\Filament\Resources\Supports\Pages\ViewSupport;
use App\Filament\Resources\Supports\Tables\SupportsTable;
use App\Models\Admin\SupportQuery;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SupportResource extends Resource
{
    protected static ?string $model = SupportQuery::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel = 'Support';
    protected static ?string $modelLabel = 'Support Query';
    protected static ?string $pluralModelLabel = 'Support Queries';
    protected static ?int $navigationSort = 9;

    // Show count badge
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 10 ? 'danger' : ($count > 5 ? 'warning' : 'success');
    }

    // =========================
    // FORM SCHEMA - Not needed for view-only
    // =========================
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    // =========================
    // TABLE SCHEMA
    // =========================
    public static function table(Table $table): Table
    {
        return SupportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupports::route('/'),
            'view' => ViewSupport::route('/{record}'),
        ];
    }
}
