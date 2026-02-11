<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Resources\DealResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Refineder\FilamentCrm\Enums\DealStage;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;

class ViewDeal extends ViewRecord
{
    protected static string $resource = DealResource::class;

    protected static string $view = 'refineder-crm::livewire.deal-view';

    protected function getHeaderActions(): array
    {
        return [
            // --- Change Stage Dropdown ---
            Action::make('change_stage')
                ->label(__('refineder-crm::deals.actions.change_stage'))
                ->icon('heroicon-o-arrows-right-left')
                ->color('gray')
                ->form([
                    \Filament\Forms\Components\Select::make('stage')
                        ->label(__('refineder-crm::deals.form.stage'))
                        ->options(
                            collect(DealStage::cases())
                                ->reject(fn (DealStage $stage) => $stage->isTerminal())
                                ->mapWithKeys(fn (DealStage $stage) => [$stage->value => $stage->label()])
                                ->toArray()
                        )
                        ->default(fn () => $this->record->stage->value)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->update(['stage' => $data['stage']]);
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('refineder-crm::deals.notifications.stage_changed'))
                        ->body(__('refineder-crm::deals.notifications.stage_changed_to', ['stage' => DealStage::from($data['stage'])->label()]))
                        ->success()
                        ->send();
                })
                ->hidden(fn () => $this->record->isClosed()),

            // --- Mark as Won ---
            Action::make('mark_won')
                ->label(__('refineder-crm::deals.actions.mark_won'))
                ->icon('heroicon-o-trophy')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('refineder-crm::deals.actions.mark_won'))
                ->modalDescription(__('refineder-crm::deals.actions.mark_won_confirmation'))
                ->action(function (): void {
                    $this->record->markAsWon();
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('refineder-crm::deals.notifications.deal_won'))
                        ->success()
                        ->send();
                })
                ->hidden(fn () => $this->record->isClosed()),

            // --- Mark as Lost ---
            Action::make('mark_lost')
                ->label(__('refineder-crm::deals.actions.mark_lost'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('refineder-crm::deals.actions.mark_lost'))
                ->modalDescription(__('refineder-crm::deals.actions.mark_lost_confirmation'))
                ->action(function (): void {
                    $this->record->markAsLost();
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('refineder-crm::deals.notifications.deal_lost'))
                        ->warning()
                        ->send();
                })
                ->hidden(fn () => $this->record->isClosed()),

            // --- Reopen Deal ---
            Action::make('reopen')
                ->label(__('refineder-crm::deals.actions.reopen'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('refineder-crm::deals.actions.reopen'))
                ->modalDescription(__('refineder-crm::deals.actions.reopen_confirmation'))
                ->action(function (): void {
                    $this->record->update([
                        'stage' => DealStage::Lead,
                        'closed_at' => null,
                    ]);
                    $this->record->refresh();

                    Notification::make()
                        ->title(__('refineder-crm::deals.notifications.deal_reopened'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->isClosed()),

            // --- Edit ---
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }
}
