<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Enums;

enum DealStage: string
{
    case Lead = 'lead';
    case Qualified = 'qualified';
    case Proposal = 'proposal';
    case Negotiation = 'negotiation';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Lead => __('refineder-crm::deals.stages.lead'),
            self::Qualified => __('refineder-crm::deals.stages.qualified'),
            self::Proposal => __('refineder-crm::deals.stages.proposal'),
            self::Negotiation => __('refineder-crm::deals.stages.negotiation'),
            self::Won => __('refineder-crm::deals.stages.won'),
            self::Lost => __('refineder-crm::deals.stages.lost'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Lead => 'gray',
            self::Qualified => 'info',
            self::Proposal => 'warning',
            self::Negotiation => 'primary',
            self::Won => 'success',
            self::Lost => 'danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Won, self::Lost]);
    }
}
