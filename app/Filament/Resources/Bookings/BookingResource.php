<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Resources\Bookings\Pages\CreateBooking;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\ViewBooking;
use App\Filament\Resources\Bookings\Schemas\BookingForm;
use App\Filament\Resources\Bookings\Schemas\BookingInfolist;
use App\Filament\Resources\Bookings\Tables\BookingsTable;
use App\Models\Vendor\Booking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Bookings';

//    protected static ?string $modelLabel = 'Booking';

//    protected static ?string $pluralModelLabel = 'Bookings';

//    protected static string|null|\UnitEnum $navigationGroup = 'Vendor Management';

    protected static ?int $navigationSort = 5;

    // Record title attribute - used in breadcrumbs and page titles
    protected static ?string $recordTitleAttribute = 'id';

    // Global search configuration
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'amount',           // Direct column on bookings table
            'host.full_name',   // Relationship column
            'host.email',       // Relationship column
        ];
    }

    // Configure what is displayed in global search results
    public static function getGlobalSearchResultTitle($record): string
    {
        return "Booking #{$record->id} - {$record->host->full_name}";
    }

    // Add additional details to global search results
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Host' => $record->host->full_name ?? 'N/A',
            'Email' => $record->host->email ?? 'N/A',
            'Amount' => $record->amount ? '$' . number_format($record->amount, 2) : 'N/A',
        ];
    }

    // Optional: Add actions to global search results
    public static function getGlobalSearchResultActions($record): array
    {
        return [
            // You can add custom actions here if needed
        ];
    }

    // Optional: Limit global search results
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['host']); // Eager load the host relationship for better performance
    }

    public static function form(Schema $schema): Schema
    {
        return BookingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingsTable::configure($table);
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
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'view' => ViewBooking::route('/{record}'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // Authorization
    public static function canViewAny(): bool
    {
        return true;
    }
}
