<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\WhatsappSessionResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Refineder\FilamentCrm\Enums\SessionStatus;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\WhatsappSessionResource;
use Refineder\FilamentCrm\Services\WasenderService;

class EditWhatsappSession extends EditRecord
{
    protected static string $resource = WhatsappSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('connect')
                ->label(__('refineder-crm::sessions.actions.connect'))
                ->icon('heroicon-o-signal')
                ->color('success')
                ->visible(fn () => $this->record->status !== SessionStatus::Connected)
                ->requiresConfirmation()
                ->action(function (WasenderService $wasender) {
                    try {
                        $wasender->connectSession($this->record);

                        Notification::make()
                            ->title(__('refineder-crm::sessions.notifications.connecting'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('refineder-crm::sessions.notifications.connect_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('disconnect')
                ->label(__('refineder-crm::sessions.actions.disconnect'))
                ->icon('heroicon-o-signal-slash')
                ->color('danger')
                ->visible(fn () => $this->record->status === SessionStatus::Connected)
                ->requiresConfirmation()
                ->action(function (WasenderService $wasender) {
                    try {
                        $wasender->disconnectSession($this->record);

                        Notification::make()
                            ->title(__('refineder-crm::sessions.notifications.disconnected'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('refineder-crm::sessions.notifications.disconnect_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            DeleteAction::make(),
        ];
    }
}
