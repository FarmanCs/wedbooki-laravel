<?php

namespace App\Filament\Resources\Communications\Tables;

use App\Models\Admin\Notification;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class CommunicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->message),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'Announcement',
                        'warning' => 'Alert',
                        'success' => 'Reminder',
                    ])
                    ->sortable(),

                BadgeColumn::make('recipients')
                    ->label('Recipients')
                    ->colors([
                        'primary' => 'Users',
                        'success' => 'Vendors',
                        'secondary' => 'All',
                    ]),

                BadgeColumn::make('delivery_method')
                    ->label('Delivery')
                    ->colors([
                        'primary' => 'Email',
                        'success' => 'SMS',
                        'warning' => 'Push Notification',
                        'secondary' => 'All',
                    ]),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                    ])
                    ->sortable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->modalHeading('Notification Details')
                    ->modalWidth('2xl')
                    ->schema([
                        TextColumn::make('title')->label('Title'),
                        TextColumn::make('message')->label('Message'),
                        BadgeColumn::make('type')->label('Type'),
                        BadgeColumn::make('recipients')->label('Recipients'),
                        BadgeColumn::make('delivery_method')->label('Delivery Method'),
                        BadgeColumn::make('send_mode')->label('Send Mode'),
                        TextColumn::make('scheduled_at')->label('Scheduled At')->dateTime('d M Y H:i'),
                        BadgeColumn::make('status')->label('Status'),
                        TextColumn::make('created_at')->label('Created')->dateTime('d M Y H:i'),
                        TextColumn::make('updated_at')->label('Updated')->dateTime('d M Y H:i'),
                    ]),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
