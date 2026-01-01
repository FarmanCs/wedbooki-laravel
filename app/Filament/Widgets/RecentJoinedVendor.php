<?php

namespace App\Filament\Widgets;

use App\Models\Vendor\Category;
use App\Models\Vendor\Vendor;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class RecentJoinedVendor extends TableWidget
{
    protected static ?string $heading = 'Recently Joined Vendors';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;
    protected static bool $isCollapsible = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => Vendor::query()
                    ->whereBetween('created_at', [
                        Carbon::now()->subWeek(),
                        Carbon::now(),
                    ])
                    ->latest()
                    ->limit(15)
            )
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('Avatar')
                    ->circular()
                    ->size(40),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('category.type')
                    ->label('Category')
                    ->badge()
                    ->sortable(),

                TextColumn::make('businesses.company_name')
                    ->label('Business')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->businesses->pluck('company_name')->join(', '))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->modalHeading('Vendor Details')
                    ->modalWidth('2xl')
                    ->schema([
                        Section::make('Basic Information')
                            ->columns(3)
                            ->schema([
                                ImageEntry::make('profile_image')
                                    ->label('Profile Image')
                                    ->circular(),

                                TextEntry::make('full_name')
                                    ->label('Full Name')
                                    ->weight('bold'),

                                TextEntry::make('email')
                                    ->label('Email'),

                                TextEntry::make('phone_no')
                                    ->label('Phone Number'),

                                TextEntry::make('country_code')
                                    ->label('Country Code'),

                                TextEntry::make('country'),

                                TextEntry::make('city'),
                            ]),

                        Section::make('Professional Details')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('category.type')
                                    ->label('Category')
                                    ->badge(),

                                TextEntry::make('years_of_experience')
                                    ->label('Experience (Years)'),

                                TextEntry::make('languages')
                                    ->label('Languages')
                                    ->formatStateUsing(
                                        fn($state) => is_array($state) ? implode(', ', $state) : '-'
                                    ),

                                TextEntry::make('specialties')
                                    ->label('Specialties')
                                    ->formatStateUsing(
                                        fn($state) => is_array($state) ? implode(', ', $state) : '-'
                                    ),

                                TextEntry::make('team_members')
                                    ->label('Team Members'),
                            ]),

                        Section::make('Businesses')
                            ->schema([
                                RepeatableEntry::make('businesses')
                                    ->schema([
                                        TextEntry::make('company_name')
                                            ->label('Company'),

                                        TextEntry::make('created_at')
                                            ->label('Joined On')
                                            ->date(),
                                    ])
                                    ->columns(2),
                            ])
                            ->collapsed(),

                        Section::make('Account Status')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('email_verified')
                                    ->label('Email Verified')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                                    ->color(fn($state) => $state ? 'success' : 'danger'),

                                TextEntry::make('profile_verification')
                                    ->label('Profile Verified')
                                    ->badge(),

                                TextEntry::make('account_deactivated')
                                    ->label('Account Status')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => $state ? 'Deactivated' : 'Active')
                                    ->color(fn($state) => $state ? 'danger' : 'success'),
                            ])
                            ->collapsed(),
                    ]),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(function () {
                        return Category::query()
                            ->whereHas('vendors', function (Builder $query) {
                                $query->whereBetween('created_at', [
                                    Carbon::now()->subWeek(),
                                    Carbon::now(),
                                ]);
                            })
                            ->get()
                            ->pluck('type', 'id');
                    })
                    ->searchable()
                    ->preload(),

                SelectFilter::make('business')
                    ->label('Business')
                    ->relationship('businesses', 'company_name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);

    }
}
