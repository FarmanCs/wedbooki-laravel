<?php

namespace App\Filament\Resources\Supports;

use App\Filament\Resources\Supports\Pages\CreateSupport;
use App\Filament\Resources\Supports\Pages\EditSupport;
use App\Filament\Resources\Supports\Pages\ListSupports;
use App\Filament\Resources\Supports\Pages\ViewSupport;
use App\Models\Admin\SupportQuery;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class SupportResource extends Resource
{
    protected static ?string $model = SupportQuery::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel = 'Support Tickets';
    protected static ?string $modelLabel = 'Support Query';
    protected static ?string $pluralModelLabel = 'Support Queries';
    protected static ?int $navigationSort = 8;

    // Show count badge
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 10 ? 'danger' : ($count > 5 ? 'warning' : 'success');
    }

    // =========================
    // FORM SCHEMA
    // =========================
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Section::make('Customer Information')
                ->schema([
                    Forms\Components\TextInput::make('full_name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('email')
                        ->required()
                        ->email()
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('phone_number')
                        ->required()
                        ->tel()
                        ->maxLength(255)
                        ->columnSpan(1),
                ])
                ->columns(3),

            Forms\Components\Section::make('Query Details')
                ->schema([
                    Forms\Components\TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('priority')
                        ->required()
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                        ])
                        ->default('low')
                        ->columnSpan(1),

                    Forms\Components\Select::make('status')
                        ->required()
                        ->options([
                            'pending' => 'Pending',
                            'resolved' => 'Resolved',
                            'rejected' => 'Rejected',
                        ])
                        ->default('pending')
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('message')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('attachments')
                        ->multiple()
                        ->directory('support-attachments')
                        ->disk('public')
                        ->maxFiles(5)
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    // =========================
    // TABLE SCHEMA
    // =========================
    public static function table(Tables\Table $table): Tables\Table
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
                    ->tooltip(fn ($record) => $record->subject)
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

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->since()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->query(fn (Builder $query) => $query->where('priority', 'high')),

                Filter::make('pending_queries')
                    ->label('Pending Only')
                    ->query(fn (Builder $query) => $query->where('status', 'pending')),

                Filter::make('created_today')
                    ->label('Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today())),

                Filter::make('created_this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'resolved']))
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'rejected']))
                    ->visible(fn ($record) => $record->status === 'pending'),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),

                BulkAction::make('mark_resolved')
                    ->label('Mark as Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->update(['status' => 'resolved']))
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('mark_pending')
                    ->label('Mark as Pending')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $records->each->update(['status' => 'pending']))
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupports::route('/'),
            'create' => CreateSupport::route('/create'),
            'edit' => EditSupport::route('/{record}/edit'),
            'view' => ViewSupport::route('/{record}'),
        ];
    }
}
