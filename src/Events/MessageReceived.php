<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Refineder\FilamentCrm\Models\CrmMessage;

class MessageReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly CrmMessage $message,
        public readonly array $rawPayload = [],
    ) {}
}
