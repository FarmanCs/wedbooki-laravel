<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Admin\AdminPackage;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Information')
                ->schema([
                    TextEntry::make('name')
                        ->label('Package Name')
                        ->icon('heroicon-o-cube')
                        ->size('sm')
                        ->weight('bold')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Silver' => 'gray',
                            'Gold' => 'warning',
                            'Platinum' => 'info',
                            default => 'gray',
                        }),

                    TextEntry::make('category.type')
                        ->label('Category')
                        ->badge()
                        ->color('primary')
                        ->icon('heroicon-o-tag'),

                    IconEntry::make('is_active')
                        ->label('Status')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')
                        ->falseIcon('heroicon-o-x-circle')
                        ->trueColor('success')
                        ->falseColor('danger'),

                    TextEntry::make('published_at')
                        ->label('Published At')
                        ->dateTime('M d, Y h:i A')
                        ->icon('heroicon-o-calendar'),
                ])
                ->columns(4),

            Section::make('Package Details')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextEntry::make('badge')
                        ->label('Badge')
                        ->badge()
                        ->color('success')
                        ->icon('heroicon-o-sparkles')
                        ->placeholder('No badge set')
                        ->visible(fn($record) => !empty($record->badge)),

                    TextEntry::make('description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->prose()
                        ->markdown(),
                ])
                ->columns(1),

            Section::make('Pricing')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('monthly_price')
                                ->label('Monthly Price')
                                ->money('usd')
                                ->size('sm')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar')
                                ->color('success'),

                            TextEntry::make('quarterly_price')
                                ->label('Quarterly Price')
                                ->money('usd')
                                ->size('sm')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar-days')
                                ->color('warning')
                                ->placeholder('Not set')
                                ->visible(fn($record) => !empty($record->quarterly_price)),

                            TextEntry::make('yearly_price')
                                ->label('Yearly Price')
                                ->money('usd')
                                ->size('sm')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar')
                                ->color('danger')
                                ->placeholder('Not set')
                                ->visible(fn($record) => !empty($record->yearly_price)),
                        ]),
                ]),

            Section::make('Features')
                ->icon('heroicon-o-check-badge')
                ->description('Features included in this package')
                ->schema([
                    TextEntry::make('features_count')
                        ->label('Total Features')
                        ->badge()
                        ->color('primary')
                        ->formatStateUsing(fn($record) => $record->features()->count() . ' features'),

                    RepeatableEntry::make('features')
                        ->label('')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Feature Name')
                                ->icon('heroicon-o-check-circle')
                                ->color('success'),

                            TextEntry::make('key')
                                ->label('Key')
                                ->badge()
                                ->color('gray')
                                ->copyable()
                                ->copyMessage('Feature key copied!')
                                ->copyMessageDuration(1500),

                            IconEntry::make('is_active')
                                ->label('Active')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->visible(fn($record) => $record->features()->count() > 0),

                    TextEntry::make('no_features')
                        ->label('')
                        ->default('No features assigned to this package')
                        ->color('warning')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->visible(fn($record) => $record->features()->count() === 0),
                ])
                ->collapsible()
                ->collapsed(false),

            Section::make('Related Packages in this Category')
                ->icon('heroicon-o-squares-2x2')
                ->description(fn($record) => "Other packages available in {$record->category->type} category")
                ->schema([
                    TextEntry::make('related_packages')
                        ->label('')
                        ->formatStateUsing(function ($record) {
                            $relatedPackages = AdminPackage::where('category_id', $record->category_id)
                                ->where('id', '!=', $record->id)
                                ->orderByRaw("FIELD(name, 'Silver', 'Gold', 'Platinum')")
                                ->get();

                            if ($relatedPackages->isEmpty()) {
                                return '<div class="text-sm text-gray-500">No other packages in this category</div>';
                            }

                            $html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                            foreach ($relatedPackages as $pkg) {
                                $badgeColor = match ($pkg->name) {
                                    'Silver' => 'bg-gray-100 text-gray-800 ring-gray-500/10',
                                    'Gold' => 'bg-yellow-100 text-yellow-800 ring-yellow-500/10',
                                    'Platinum' => 'bg-blue-100 text-blue-800 ring-blue-500/10',
                                    default => 'bg-gray-100 text-gray-800 ring-gray-500/10'
                                };

                                $statusColor = $pkg->is_active
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800';

                                $statusText = $pkg->is_active ? 'Active' : 'Inactive';

                                $html .= "
                        <div class='rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800'>
                            <div class='flex items-start justify-between mb-3'>
                                <div class='flex items-center gap-2'>
                                    <span class='inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {$badgeColor}'>
                                        {$pkg->name}
                                    </span>
                                    <span class='inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {$statusColor}'>
                                        {$statusText}
                                    </span>
                                </div>
                    ";

                                if ($pkg->badge) {
                                    $html .= "
                            <span class='inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20'>
                                {$pkg->badge}
                            </span>
                        ";
                                }

                                $html .= "</div>";

                                if ($pkg->description) {
                                    $html .= "
                            <p class='text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2'>
                                {$pkg->description}
                            </p>
                        ";
                                }

                                $html .= "
                        <div class='space-y-2 border-t border-gray-100 dark:border-gray-700 pt-3'>
                            <div class='flex items-center justify-between'>
                                <span class='text-xs text-gray-500 dark:text-gray-400'>Monthly Price</span>
                                <span class='text-sm font-semibold text-green-600 dark:text-green-400'>
                                    $" . number_format($pkg->monthly_price, 2) . "
                                </span>
                            </div>
                    ";

                                if ($pkg->quarterly_price) {
                                    $html .= "
                            <div class='flex items-center justify-between'>
                                <span class='text-xs text-gray-500 dark:text-gray-400'>Quarterly Price</span>
                                <span class='text-sm font-semibold text-yellow-600 dark:text-yellow-400'>
                                    $" . number_format($pkg->quarterly_price, 2) . "
                                </span>
                            </div>
                        ";
                                }

                                if ($pkg->yearly_price) {
                                    $html .= "
                            <div class='flex items-center justify-between'>
                                <span class='text-xs text-gray-500 dark:text-gray-400'>Yearly Price</span>
                                <span class='text-sm font-semibold text-red-600 dark:text-red-400'>
                                    $" . number_format($pkg->yearly_price, 2) . "
                                </span>
                            </div>
                        ";
                                }

                                $featuresCount = $pkg->features()->count();
                                $html .= "
                            <div class='flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700'>
                                <span class='text-xs text-gray-500 dark:text-gray-400'>Features</span>
                                <span class='inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10'>
                                    {$featuresCount} features
                                </span>
                            </div>
                        </div>
                    </div>
                    ";
                            }

                            $html .= '</div>';

                            return $html;
                        })
                        ->html()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false),
        ]);
    }
}
