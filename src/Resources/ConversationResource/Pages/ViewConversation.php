<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\ConversationResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Refineder\FilamentCrm\Enums\ConversationStatus;
use Refineder\FilamentCrm\Resources\ConversationResource\ConversationResource;

class ViewConversation extends ViewRecord
{
    protected static string $resource = ConversationResource::class;

    protected static string $view = 'refineder-crm::livewire.conversation-view';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('close')
                ->label(__('refineder-crm::conversations.actions.close'))
                ->icon('heroicon-o-check-circle')
                ->color('warning')
                ->visible(fn () => $this->record->status !== ConversationStatus::Closed)
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => ConversationStatus::Closed]);

                    Notification::make()
                        ->title(__('refineder-crm::conversations.notifications.closed'))
                        ->success()
                        ->send();
                }),

            Action::make('reopen')
                ->label(__('refineder-crm::conversations.actions.reopen'))
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->visible(fn () => $this->record->status === ConversationStatus::Closed)
                ->action(function () {
                    $this->record->update(['status' => ConversationStatus::Open]);

                    Notification::make()
                        ->title(__('refineder-crm::conversations.notifications.reopened'))
                        ->success()
                        ->send();
                }),

            Action::make('archive')
                ->label(__('refineder-crm::conversations.actions.archive'))
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->archive();

                    Notification::make()
                        ->title(__('refineder-crm::conversations.notifications.archived'))
                        ->success()
                        ->send();

                    $this->redirect(ConversationResource::getUrl());
                }),
        ];
    }
}
