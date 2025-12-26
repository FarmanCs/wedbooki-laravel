<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Admin\Feature;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class SubscriptionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Information')
                ->description('Select a category to automatically create Silver, Gold, and Platinum packages')
                ->schema([
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'type')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Reset all tier fields when category changes
                            $tiers = ['silver', 'gold', 'platinum'];
                            foreach ($tiers as $tier) {
                                $set("{$tier}_description", null);
                                $set("{$tier}_badge", null);
                                $set("{$tier}_monthly_price", null);
                                $set("{$tier}_quarterly_price", null);
                                $set("{$tier}_yearly_price", null);
                                $set("{$tier}_features", []);
                            }
                        })
                        ->helperText('All three packages (Silver, Gold, Platinum) will be created for this category'),
                ])
                ->columns(1),

            Section::make('Silver Tier')
                ->description('Configure the Silver package details and features')
                ->schema([
                    Textarea::make('silver_description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    TextInput::make('silver_badge')
                        ->label('Badge')
                        ->maxLength(50)
                        ->placeholder('e.g., Most Popular, Best Value'),

                    TextInput::make('silver_monthly_price')
                        ->label('Monthly Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('silver_quarterly_price')
                        ->label('Quarterly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('silver_yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    Select::make('silver_features')
                        ->label('Features')
                        ->multiple()
                        ->options(fn() => Feature::where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn($feature) => [
                                $feature->id => $feature->name . ($feature->description ? " - {$feature->description}" : '')
                            ])
                            ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->minItems(1)
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Feature Name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, callable $set) => $set('key', Str::slug($state))
                                ),

                            TextInput::make('key')
                                ->label('Feature Key')
                                ->required()
                                ->unique(Feature::class, 'key', ignoreRecord: true)
                                ->maxLength(255)
                                ->helperText('Auto-generated from name, must be unique'),

                            Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->maxLength(255),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $feature = Feature::create($data);
                            return $feature->id;
                        })
                        ->createOptionModalHeading('Create New Feature')
                        ->createOptionAction(function (Action $action) {
                            return $action
                                ->modalHeading('Create New Feature')
                                ->modalSubmitActionLabel('Create Feature')
                                ->modalWidth('lg');
                        })
                        ->helperText('Select existing features or create new ones. Features will be linked to this package.'),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Gold Tier')
                ->description('Configure the Gold package details and features')
                ->schema([
                    Textarea::make('gold_description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    TextInput::make('gold_badge')
                        ->label('Badge')
                        ->maxLength(50)
                        ->placeholder('e.g., Most Popular, Best Value'),

                    TextInput::make('gold_monthly_price')
                        ->label('Monthly Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('gold_quarterly_price')
                        ->label('Quarterly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('gold_yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    Select::make('gold_features')
                        ->label('Features')
                        ->multiple()
                        ->options(fn() => Feature::where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn($feature) => [
                                $feature->id => $feature->name . ($feature->description ? " - {$feature->description}" : '')
                            ])
                            ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->minItems(1)
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Feature Name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, callable $set) => $set('key', Str::slug($state))
                                ),

                            TextInput::make('key')
                                ->label('Feature Key')
                                ->required()
                                ->unique(Feature::class, 'key', ignoreRecord: true)
                                ->maxLength(255)
                                ->helperText('Auto-generated from name, must be unique'),

                            Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->maxLength(255),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $feature = Feature::create($data);
                            return $feature->id;
                        })
                        ->createOptionModalHeading('Create New Feature')
                        ->createOptionAction(function (Action $action) {
                            return $action
                                ->modalHeading('Create New Feature')
                                ->modalSubmitActionLabel('Create Feature')
                                ->modalWidth('lg');
                        })
                        ->helperText('Select existing features or create new ones. Features will be linked to this package.'),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Platinum Tier')
                ->description('Configure the Platinum package details and features')
                ->schema([
                    Textarea::make('platinum_description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    TextInput::make('platinum_badge')
                        ->label('Badge')
                        ->maxLength(50)
                        ->placeholder('e.g., Most Popular, Best Value'),

                    TextInput::make('platinum_monthly_price')
                        ->label('Monthly Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('platinum_quarterly_price')
                        ->label('Quarterly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('platinum_yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    Select::make('platinum_features')
                        ->label('Features')
                        ->multiple()
                        ->options(fn() => Feature::where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn($feature) => [
                                $feature->id => $feature->name . ($feature->description ? " - {$feature->description}" : '')
                            ])
                            ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->minItems(1)
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Feature Name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, callable $set) => $set('key', Str::slug($state))
                                ),

                            TextInput::make('key')
                                ->label('Feature Key')
                                ->required()
                                ->unique(Feature::class, 'key', ignoreRecord: true)
                                ->maxLength(255)
                                ->helperText('Auto-generated from name, must be unique'),

                            Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->maxLength(255),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            $feature = Feature::create($data);
                            return $feature->id;
                        })
                        ->createOptionModalHeading('Create New Feature')
                        ->createOptionAction(function (Action $action) {
                            return $action
                                ->modalHeading('Create New Feature')
                                ->modalSubmitActionLabel('Create Feature')
                                ->modalWidth('lg');
                        })
                        ->helperText('Select existing features or create new ones. Features will be linked to this package.'),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Package Settings')
                ->description('Configure additional package settings')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->onIcon(Heroicon::Bolt)
                        ->default(true)
                        ->helperText('Only active packages will be visible to users'),

                    DateTimePicker::make('published_at')
                        ->label('Publish Date')
                        ->default(now())
                        ->helperText('Set when this package should be published'),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }
}
