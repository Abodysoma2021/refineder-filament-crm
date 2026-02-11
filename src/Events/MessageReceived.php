<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Refineder\FilamentCrm\Models\CrmMessage;

class MessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly CrmMessage $message,
        public readonly array $rawPayload = [],
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * Broadcasts on both the conversation-specific channel (for ChatBox)
     * and the user-level channel (for global notifications on any page).
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel("crm.conversation.{$this->message->conversation_id}"),
        ];

        // Also broadcast on user channel for global notifications
        $conversation = $this->message->conversation;
        if ($conversation && $conversation->user_id) {
            $channels[] = new PrivateChannel("crm.user.{$conversation->user_id}");
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $conversation = $this->message->conversation;
        $contact = $conversation?->contact;

        // Find the active deal for this conversation
        $deal = null;
        if ($conversation) {
            $dealModel = config('refineder-crm.models.deal', \Refineder\FilamentCrm\Models\CrmDeal::class);
            $deal = $dealModel::where('conversation_id', $conversation->id)
                ->whereNotIn('stage', ['won', 'lost'])
                ->latest()
                ->first();
        }

        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'body' => $this->message->body,
            'type' => $this->message->type->value,
            'is_from_me' => $this->message->is_from_me,
            'status' => $this->message->status->value,
            'media_url' => $this->message->media_url,
            'time' => $this->message->created_at->format('H:i'),
            'date' => $this->message->created_at->format('Y-m-d'),
            'contact_name' => $contact?->getDisplayName() ?? 'Unknown',
            'contact_phone' => $contact?->phone,
            'deal_id' => $deal?->id,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.received';
    }
}
