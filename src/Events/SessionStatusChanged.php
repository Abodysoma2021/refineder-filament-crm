<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Refineder\FilamentCrm\Enums\SessionStatus;
use Refineder\FilamentCrm\Models\WhatsappSession;

class SessionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly WhatsappSession $session,
        public readonly SessionStatus $previousStatus,
        public readonly SessionStatus $newStatus,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("crm.session.{$this->session->id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'previous_status' => $this->previousStatus->value,
            'new_status' => $this->newStatus->value,
            'status_label' => $this->newStatus->label(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'session.status.changed';
    }
}
