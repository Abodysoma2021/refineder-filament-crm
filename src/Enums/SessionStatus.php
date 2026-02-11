<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Enums;

enum SessionStatus: string
{
    case Connected = 'connected';
    case Disconnected = 'disconnected';
    case Connecting = 'connecting';
    case QrPending = 'qr_pending';

    public function label(): string
    {
        return match ($this) {
            self::Connected => __('refineder-crm::sessions.statuses.connected'),
            self::Disconnected => __('refineder-crm::sessions.statuses.disconnected'),
            self::Connecting => __('refineder-crm::sessions.statuses.connecting'),
            self::QrPending => __('refineder-crm::sessions.statuses.qr_pending'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Connected => 'success',
            self::Disconnected => 'danger',
            self::Connecting => 'warning',
            self::QrPending => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Connected => 'heroicon-o-signal',
            self::Disconnected => 'heroicon-o-signal-slash',
            self::Connecting => 'heroicon-o-arrow-path',
            self::QrPending => 'heroicon-o-qr-code',
        };
    }
}
