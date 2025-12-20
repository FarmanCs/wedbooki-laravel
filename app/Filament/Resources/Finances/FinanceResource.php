<?php

namespace App\Filament\Resources\Finances;

use App\Filament\Resources\Finances\Pages\CreateFinance;
use App\Filament\Resources\Finances\Pages\EditFinance;
use App\Filament\Resources\Finances\Pages\ListFinances;
use App\Filament\Resources\Finances\Pages\ViewFinance;
use App\Filament\Resources\Finances\Schemas\FinanceForm;
use App\Filament\Resources\Finances\Schemas\FinanceInfolist;
use App\Filament\Resources\Finances\Tables\FinancesTable;
use App\Models\Admin\Transaction;
use App\Models\Finance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinanceResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'Transaction';
    protected static ?int $navigationSort = 6;


    public static function form(Schema $schema): Schema
    {
        return FinanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FinanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinancesTable::configure($table);
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
            'index' => ListFinances::route('/'),
            'create' => CreateFinance::route('/create'),
            'view' => ViewFinance::route('/{record}'),
            'edit' => EditFinance::route('/{record}/edit'),
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
