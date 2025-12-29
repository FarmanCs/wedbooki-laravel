<?php

namespace App\Filament\Resources\Supports\Tables;


use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->icon('heroicon-m-phone')
                    ->copyable(),

                TextColumn::make('subject')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->subject)
                    ->searchable()
                    ->wrap(),

                BadgeColumn::make('priority')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                    ])
                    ->icons([
                        'heroicon-o-arrow-down' => 'low',
                        'heroicon-o-minus' => 'medium',
                        'heroicon-o-arrow-up' => 'high',
                    ])
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'resolved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'resolved',
                        'heroicon-o-x-circle' => 'rejected',
                    ])
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low Priority',
                        'medium' => 'Medium Priority',
                        'high' => 'High Priority',
                    ])
                    ->multiple(),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'resolved' => 'Resolved',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple(),

                Filter::make('high_priority')
                    ->label('High Priority Only')
                    ->query(fn(Builder $query) => $query->where('priority', 'high')),

                Filter::make('pending_queries')
                    ->label('Pending Only')
                    ->query(fn(Builder $query) => $query->where('status', 'pending')),

                Filter::make('created_today')
                    ->label('Today')
                    ->query(fn(Builder $query) => $query->whereDate('created_at', today())),

                Filter::make('created_this_week')
                    ->label('This Week')
                    ->query(fn(Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'resolved']))
                    ->visible(fn($record) => $record->status === 'pending')
                    ->successNotificationTitle('Query resolved successfully!'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'rejected']))
                    ->visible(fn($record) => $record->status === 'pending')
                    ->successNotificationTitle('Query rejected!'),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),

                    BulkAction::make('mark_resolved')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => 'resolved']))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Selected queries marked as resolved!'),

                    BulkAction::make('mark_pending')
                        ->label('Mark as Pending')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => 'pending']))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Selected queries marked as pending!'),

                    BulkAction::make('mark_rejected')
                        ->label('Mark as Rejected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['status' => 'rejected']))
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Selected queries marked as rejected!'),
                ]),
            ]);
    }
}
