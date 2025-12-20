<?php

namespace App\Filament\Resources\Subscriptions;

use App\Filament\Resources\Subscriptions\Pages\CreateSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\EditSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\ViewSubscriptions;
use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsForm;
use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsInfolist;
use App\Filament\Resources\Subscriptions\Tables\SubscriptionsTable;
//use App\Models\Subscriptions;
use App\Models\Vendor\Subscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionsResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'Subscriptions';

    protected static  ?int $navigationSort=4;


    public static function form(Schema $schema): Schema
    {
        return SubscriptionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubscriptionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionsTable::configure($table);
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
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscriptions::route('/create'),
            'view' => ViewSubscriptions::route('/{record}'),
            'edit' => EditSubscriptions::route('/{record}/edit'),
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
