<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Livewire;

use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\Component;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Models\CrmMessage;
use Refineder\FilamentCrm\Services\WasenderService;

class ChatBox extends Component
{
    public ?int $conversationId = null;

    public string $messageText = '';

    public array $messages = [];

    public ?CrmConversation $conversation = null;

    public function mount(int $conversationId): void
    {
        $this->conversationId = $conversationId;
        $this->loadConversation();
        $this->loadMessages();

        // Mark as read when opened
        $this->conversation?->markAsRead();
    }

    /**
     * Get the listeners for Echo events.
     * This enables real-time message receiving via Laravel Reverb/Echo.
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        return [
            "echo-private:crm.conversation.{$this->conversationId},.message.received" => 'onMessageReceived',
            "echo-private:crm.conversation.{$this->conversationId},.message.sent" => 'onMessageSent',
        ];
    }

    /**
     * Handle a real-time message received event from Echo.
     */
    public function onMessageReceived(array $data): void
    {
        $this->loadMessages();
        $this->conversation?->markAsRead();
        $this->dispatch('message-received');
    }

    /**
     * Handle a real-time message sent event from Echo.
     */
    public function onMessageSent(array $data): void
    {
        $this->loadMessages();
    }

    public function loadConversation(): void
    {
        $this->conversation = CrmConversation::with(['contact', 'whatsappSession'])
            ->find($this->conversationId);
    }

    public function loadMessages(): void
    {
        if (! $this->conversation) {
            return;
        }

        $this->messages = $this->conversation
            ->messages()
            ->with('repliedTo')
            ->orderBy('created_at', 'asc')
            ->latest()
            ->take(100)
            ->get()
            ->sortBy('created_at')
            ->values()
            ->map(fn (CrmMessage $msg) => [
                'id' => $msg->id,
                'body' => $msg->body,
                'type' => $msg->type->value,
                'type_label' => $msg->type->label(),
                'type_icon' => $msg->type->icon(),
                'is_from_me' => $msg->is_from_me,
                'status' => $msg->status->value,
                'status_label' => $msg->status->label(),
                'status_icon' => $msg->status->icon(),
                'status_color' => $msg->status->color(),
                'media_url' => $msg->media_url,
                'media_mime_type' => $msg->media_mime_type,
                'is_media' => $msg->isMedia(),
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->format('Y-m-d'),
                'human_time' => $msg->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function sendMessage(): void
    {
        if (empty(trim($this->messageText)) || ! $this->conversation) {
            return;
        }

        $session = $this->conversation->whatsappSession;

        if (! $session || ! $session->isConnected()) {
            Notification::make()
                ->title(__('refineder-crm::chat.session_not_connected'))
                ->danger()
                ->send();

            return;
        }

        try {
            /** @var WasenderService $wasender */
            $wasender = app(WasenderService::class);
            $wasender->sendText($session, $this->conversation, $this->messageText);

            $this->messageText = '';
            $this->loadMessages();

            $this->dispatch('message-sent');
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('refineder-crm::chat.send_failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Poll for new messages (fallback when Echo/Reverb is not available).
     */
    public function pollMessages(): void
    {
        $this->loadMessages();
        $this->conversation?->markAsRead();
    }

    public function render()
    {
        return view('refineder-crm::livewire.chat-box');
    }
}
