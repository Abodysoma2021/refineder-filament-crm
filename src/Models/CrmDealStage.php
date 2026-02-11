<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmDealStage extends Model
{
    protected $table = 'crm_deal_stages';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
            'order' => 'integer',
        ];
    }

    // --- Relationships ---

    public function deals(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.deal'), 'deal_stage_id');
    }

    // --- Scopes ---

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // --- Helpers ---

    public function isTerminal(): bool
    {
        return $this->is_won || $this->is_lost;
    }
}
