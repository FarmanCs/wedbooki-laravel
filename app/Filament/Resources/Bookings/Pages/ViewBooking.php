<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;



    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Eager load relationships
        $this->record->load(['host', 'business', 'vendor', 'package', 'extra_services']);

        return $data;
    }
    protected function getHeaderActions(): array
    {
        return [
//            EditAction::make(),
        ];
    }
}
