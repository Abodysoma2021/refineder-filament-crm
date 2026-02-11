<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Enums;

enum ConversationStatus: string
{
    case Open = 'open';
    case Pending = 'pending';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('refineder-crm::conversations.statuses.open'),
            self::Pending => __('refineder-crm::conversations.statuses.pending'),
            self::Closed => __('refineder-crm::conversations.statuses.closed'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'success',
            self::Pending => 'warning',
            self::Closed => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Open => 'heroicon-o-chat-bubble-left-ellipsis',
            self::Pending => 'heroicon-o-clock',
            self::Closed => 'heroicon-o-check-circle',
        };
    }
}
