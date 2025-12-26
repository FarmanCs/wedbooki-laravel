<?php

namespace App\Filament\Resources\Subscriptions;

use App\Filament\Resources\Subscriptions\Pages\CreateSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\EditSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\Subscriptions\Pages\ViewSubscriptions;
use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsEditForm;
use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsForm;
use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsInfolist;
use App\Filament\Resources\Subscriptions\Tables\SubscriptionsTable;
use App\Models\Admin\AdminPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionsResource extends Resource
{
    protected static ?string $model = AdminPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel="Subscriptions";


    protected static ?int $navigationSort = 4;

    //this will work the same but more dynomically for both editing and creating
    public static function form(Schema $schema): Schema
    {
        // Default form - will be overridden by pages
        return SubscriptionsForm::configure($schema);
    }


    //working the same way to handle the thing in the main resurces file  like this
//    public static function form(Schema $schema): Schema
//    {
//        // Check if we're in edit mode by looking at the current Livewire component
//        $livewire = $schema->getLivewire();
//
//        // If the component has a record property and it's not null, we're editing
//        if ($livewire && property_exists($livewire, 'record') && $livewire->record) {
//            return $schema->schema(SubscriptionsEditForm::getSchema());
//        }
//
//        // Use create form schema (creates 3 packages)
//        return SubscriptionsForm::configure($schema);
//    }

//    public static function form(Schema $schema): Schema
//    {
//        $record = request()->route('record'); // get the model being edited
//
//        if ($record) {
//            // If $record exists, we are editing
//            return $schema->schema(SubscriptionsEditForm::getSchema());
//        }
//
//        // Use create form schema (creates 3 packages)
//        return SubscriptionsForm::configure($schema);
//    }

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
