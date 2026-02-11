<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Enums;

enum MessageStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Read = 'read';
    case Failed = 'failed';
    case Received = 'received';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('refineder-crm::messages.statuses.pending'),
            self::Sent => __('refineder-crm::messages.statuses.sent'),
            self::Delivered => __('refineder-crm::messages.statuses.delivered'),
            self::Read => __('refineder-crm::messages.statuses.read'),
            self::Failed => __('refineder-crm::messages.statuses.failed'),
            self::Received => __('refineder-crm::messages.statuses.received'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Sent => 'info',
            self::Delivered => 'primary',
            self::Read => 'success',
            self::Failed => 'danger',
            self::Received => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Sent => 'heroicon-o-check',
            self::Delivered => 'heroicon-o-check-badge',
            self::Read => 'heroicon-o-eye',
            self::Failed => 'heroicon-o-x-circle',
            self::Received => 'heroicon-o-inbox-arrow-down',
        };
    }
}
