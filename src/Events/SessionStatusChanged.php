<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Refineder\FilamentCrm\Enums\SessionStatus;
use Refineder\FilamentCrm\Models\WhatsappSession;

class SessionStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly WhatsappSession $session,
        public readonly SessionStatus $previousStatus,
        public readonly SessionStatus $newStatus,
    ) {}
}
