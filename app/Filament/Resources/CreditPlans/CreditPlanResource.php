<?php

namespace App\Filament\Resources\CreditPlans;

use App\Filament\Resources\CreditPlans\Pages\CreateCreditPlan;
use App\Filament\Resources\CreditPlans\Pages\EditCreditPlan;
use App\Filament\Resources\CreditPlans\Pages\ListCreditPlans;
use App\Filament\Resources\CreditPlans\Pages\ViewCreditPlan;
use App\Filament\Resources\CreditPlans\Schemas\CreditPlanForm;
use App\Filament\Resources\CreditPlans\Schemas\CreditPlanInfolist;
use App\Filament\Resources\CreditPlans\Tables\CreditPlansTable;
use App\Models\Admin\CreditPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditPlanResource extends Resource
{
    protected static ?string $model = CreditPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CreditPlan';

    protected static ?string $navigationLabel = 'Credit Management';

    public static function form(Schema $schema): Schema
    {
        return CreditPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CreditPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreditPlansTable::configure($table);
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
            'index' => ListCreditPlans::route('/'),
            'create' => CreateCreditPlan::route('/create'),
            'view' => ViewCreditPlan::route('/{record}'),
            'edit' => EditCreditPlan::route('/{record}/edit'),
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
