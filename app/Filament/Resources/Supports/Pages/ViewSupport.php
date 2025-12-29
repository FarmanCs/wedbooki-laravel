<?php

namespace App\Filament\Resources\Supports\Pages;

use App\Filament\Resources\Supports\SupportResource;
use App\Filament\Resources\Supports\Schemas\SupportInfolist;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewSupport extends ViewRecord
{
    protected static string $resource = SupportResource::class;

    protected function getHeaderActions(): array
    {
        return [
           Action::make('resolve')
                ->label('Mark as Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Resolve Support Query?')
                ->modalDescription('Are you sure you want to mark this support query as resolved?')
                ->action(fn() => $this->record->update(['status' => 'resolved']))
                ->visible(fn() => $this->record->status === 'pending')
                ->successNotificationTitle('Support query resolved successfully!')
                ->after(fn() => $this->redirect($this->getResource()::getUrl('index'))),

          Action::make('reject')
                ->label('Reject Query')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Support Query?')
                ->modalDescription('Are you sure you want to reject this support query? This action cannot be easily undone.')
                ->action(fn() => $this->record->update(['status' => 'rejected']))
                ->visible(fn() => $this->record->status === 'pending')
                ->successNotificationTitle('Support query rejected!')
                ->after(fn() => $this->redirect($this->getResource()::getUrl('index'))),

          Action::make('reopen')
                ->label('Reopen Ticket')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reopen Support Query?')
                ->modalDescription('This will set the status back to pending.')
                ->action(fn() => $this->record->update(['status' => 'pending']))
                ->visible(fn() => in_array($this->record->status, ['resolved', 'rejected']))
                ->successNotificationTitle('Support query reopened!'),

          DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->successRedirectUrl(fn() => $this->getResource()::getUrl('index')),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema(SupportInfolist::configure(new \Filament\Schemas\Schema())->getComponents());
    }
}
