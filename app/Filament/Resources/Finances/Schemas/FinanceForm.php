<?php

namespace App\Filament\Resources\Finances\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Vendor\Booking;
use App\Models\Host\Host;
use App\Models\Vendor\Vendor;

class FinanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }
}
