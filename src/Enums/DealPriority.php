<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Enums;

enum DealPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => __('refineder-crm::deals.priorities.low'),
            self::Medium => __('refineder-crm::deals.priorities.medium'),
            self::High => __('refineder-crm::deals.priorities.high'),
            self::Urgent => __('refineder-crm::deals.priorities.urgent'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'gray',
            self::Medium => 'info',
            self::High => 'warning',
            self::Urgent => 'danger',
        };
    }
}
