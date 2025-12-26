<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Admin\Feature;
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
    /**
     * All available package tiers
     */
    private static array $tiers = ['silver', 'gold', 'platinum'];

    /**
     * Entry point used by Filament
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            self::packageInfoSection(),
            ...array_map(fn($tier) => self::tierSection($tier), self::$tiers),
            self::settingsSection(),
        ]);
    }

    /**
     * -----------------------------
     * Sections
     * -----------------------------
     */

    private static function packageInfoSection(): Section
    {
        return Section::make('Package Information')
            ->schema([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'type')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(
                        fn($state, callable $set) => self::resetTiers($set)
                    )
                    ->helperText(
                        'Changing category will reset Silver, Gold and Platinum packages'
                    ),
            ])
            ->columns(1);
    }

    private static function tierSection(string $tier): Section
    {
        $label = ucfirst($tier);

        return Section::make("{$label} Tier")
            ->schema([
                Textarea::make("{$tier}_description")
                    ->label('Description')
                    ->required()
                    ->rows(3),

                TextInput::make("{$tier}_badge")
                    ->label('Badge')
                    ->maxLength(50),

                self::priceInput("{$tier}_monthly_price", 'Monthly')
                    ->required(),

                self::priceInput("{$tier}_quarterly_price", 'Quarterly'),

                self::priceInput("{$tier}_yearly_price", 'Yearly'),

                self::featureSelect("{$tier}_features")
                    ->required()
                    ->minItems(1),
            ])
            ->columns(2)
            ->collapsible();
    }

    private static function settingsSection(): Section
    {
        return Section::make('Package Settings')
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
            ->collapsible();
    }

    /**
     * -----------------------------
     * Reusable Field Builders
     * -----------------------------
     */

    private static function priceInput(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label("{$label} Price")
            ->numeric()
            ->prefix('$')
            ->minValue(0)
            ->step(0.01);
    }

    private static function featureSelect(string $name): Select
    {
        return Select::make($name)
            ->label('Features')
            ->multiple()
            ->searchable()
            ->preload()
            ->options(
                Feature::where('is_active', true)
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->createOptionForm(self::featureForm())
            ->createOptionUsing(
                fn(array $data): int => Feature::create($data)->id
            );
    }

    private static function featureForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Feature Name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(
                    fn($state, callable $set) => $set('key', Str::slug($state))
                ),

            TextInput::make('key')
                ->label('Feature Key')
                ->required()
                ->unique(Feature::class, 'key'),

            Textarea::make('description')
                ->label('Description')
                ->rows(2),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ];
    }

    /**
     * -----------------------------
     * Helpers
     * -----------------------------
     */

    private static function resetTiers(callable $set): void
    {
        foreach (self::$tiers as $tier) {
            $set("{$tier}_description", null);
            $set("{$tier}_badge", null);
            $set("{$tier}_monthly_price", null);
            $set("{$tier}_quarterly_price", null);
            $set("{$tier}_yearly_price", null);
            $set("{$tier}_features", []);
        }
    }
}
