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
            Action::make('refresh_status')
                ->label(__('refineder-crm::sessions.actions.refresh_status'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (WasenderService $wasender) {
                    try {
                        $newStatus = $wasender->syncSessionStatus($this->record);
                        $this->record->refresh();

                        Notification::make()
                            ->title(__('refineder-crm::sessions.notifications.status_synced'))
                            ->body(__('refineder-crm::sessions.notifications.current_status', ['status' => $newStatus->label()]))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('refineder-crm::sessions.notifications.sync_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('connect')
                ->label(__('refineder-crm::sessions.actions.connect'))
                ->icon('heroicon-o-signal')
                ->color('success')
                ->visible(fn () => $this->record->status !== SessionStatus::Connected)
                ->requiresConfirmation()
                ->action(function (WasenderService $wasender) {
                    try {
                        $wasender->connectSession($this->record);
                        $this->record->refresh();

                        $statusLabel = $this->record->status->label();

                        if ($this->record->status === SessionStatus::Connected) {
                            Notification::make()
                                ->title(__('refineder-crm::sessions.notifications.connected'))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('refineder-crm::sessions.notifications.connecting'))
                                ->body(__('refineder-crm::sessions.notifications.current_status', ['status' => $statusLabel]))
                                ->info()
                                ->send();
                        }
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
                        $this->record->refresh();

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
