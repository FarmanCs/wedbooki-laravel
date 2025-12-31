<?php

namespace App\Filament\Resources\Communications\Pages;

use App\Filament\Resources\Communications\CommunicationsResource;
use App\Filament\Resources\Communications\Schemas\CommunicationsInfolist;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCommunications extends ViewRecord
{
    protected static string $resource = CommunicationsResource::class;

    public function getHeading(): string
    {
        return $this->record->title ?? 'View Communication';
    }

    public function getSubheading(): ?string
    {
        $type = ucfirst($this->record->type ?? 'Communication');
        $status = ucfirst($this->record->status ?? 'Unknown');

        return "{$type} - Status: {$status}";
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema(CommunicationsInfolist::schema());
    }

    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),

            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Delete Communication')
                ->modalDescription('Are you sure you want to delete this communication? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it'),

            Actions\Action::make('duplicate')
                ->label('Duplicate')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function () {
                    $newRecord = $this->record->replicate();
                    $newRecord->title = $this->record->title . ' (Copy)';
                    $newRecord->status = 'draft';
                    $newRecord->save();

                    $this->redirect(static::$resource::getUrl('edit', ['record' => $newRecord]));
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'sent'),

            Actions\Action::make('send_now')
                ->label('Send Now')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action(function () {
                    $this->record->update(['status' => 'sent']);
                })
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['draft', 'scheduled'])),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::$resource::getUrl('index') => 'Communications',
            $this->getHeading(),
        ];
    }
}
