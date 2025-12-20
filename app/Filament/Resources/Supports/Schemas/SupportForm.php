<?php

namespace App\Filament\Resources\Supports\Schemas;

use Filament\Schemas\Schema;

class SupportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
