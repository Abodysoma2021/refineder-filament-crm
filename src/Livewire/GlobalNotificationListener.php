<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Livewire;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;

class GlobalNotificationListener extends Component
{
    public int $userId;

    public function mount(): void
    {
        $this->userId = auth()->id() ?? 0;
    }

    /**
     * Livewire Echo listeners.
     * Listens on the user-level channel for new incoming messages across ALL conversations.
     */
    public function getListeners(): array
    {
        if (! $this->userId) {
            return [];
        }

        return [
            "echo-private:crm.user.{$this->userId},.message.received" => 'onGlobalMessageReceived',
        ];
    }

    /**
     * Handle incoming message broadcast on the user channel.
     */
    public function onGlobalMessageReceived(array $data): void
    {
        $contactName = $data['contact_name'] ?? __('refineder-crm::notifications.unknown_contact');
        $body = $data['body'] ?? '';
        $dealId = $data['deal_id'] ?? null;

        // Build the notification
        $notification = Notification::make()
            ->title(__('refineder-crm::notifications.new_message_title', ['contact' => $contactName]))
            ->body(Str::limit($body, 80))
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->iconColor('success')
            ->duration(8000);

        // Add action to open the deal if one exists
        if ($dealId) {
            $notification->actions([
                Action::make('view_deal')
                    ->label(__('refineder-crm::notifications.view_deal'))
                    ->url(DealResource::getUrl('view', ['record' => $dealId]))
                    ->button()
                    ->color('primary')
                    ->size('sm'),
            ]);
        }

        $notification->send();

        // Play notification sound via Alpine.js
        $this->dispatch('play-notification-sound');

        // Trigger widget/table refresh across the app
        $this->dispatch('crm-new-message');
    }

    public function render(): string
    {
        return <<<'BLADE'
        <div
            x-data="{
                audioCtx: null,
                playBeep() {
                    try {
                        if (!this.audioCtx) {
                            this.audioCtx = new (window.AudioContext || window.webkitAudioContext)()
                        }
                        const ctx = this.audioCtx
                        const now = ctx.currentTime

                        // First tone (higher pitch)
                        const osc1 = ctx.createOscillator()
                        const gain1 = ctx.createGain()
                        osc1.connect(gain1)
                        gain1.connect(ctx.destination)
                        osc1.frequency.value = 830
                        osc1.type = 'sine'
                        gain1.gain.setValueAtTime(0.3, now)
                        gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.15)
                        osc1.start(now)
                        osc1.stop(now + 0.15)

                        // Second tone (slightly higher, delayed)
                        const osc2 = ctx.createOscillator()
                        const gain2 = ctx.createGain()
                        osc2.connect(gain2)
                        gain2.connect(ctx.destination)
                        osc2.frequency.value = 990
                        osc2.type = 'sine'
                        gain2.gain.setValueAtTime(0.3, now + 0.12)
                        gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.3)
                        osc2.start(now + 0.12)
                        osc2.stop(now + 0.3)
                    } catch (e) {
                        console.warn('CRM: Could not play notification sound', e)
                    }
                }
            }"
            x-on:play-notification-sound.window="playBeep()"
            class="hidden"
            wire:ignore
        ></div>
        BLADE;
    }
}
