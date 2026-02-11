<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Refineder\FilamentCrm\Enums\DealPriority;
use Refineder\FilamentCrm\Enums\DealStage;

class CrmDeal extends Model
{
    protected $table = 'crm_deals';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'stage' => DealStage::class,
            'priority' => DealPriority::class,
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'closed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // --- Relationships ---

    public function contact(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.contact'), 'contact_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.conversation'), 'conversation_id');
    }

    public function dealStage(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.deal_stage'), 'deal_stage_id');
    }

    /**
     * Get all messages for this deal through its conversation.
     */
    public function messages(): HasManyThrough
    {
        $conversationModel = config('refineder-crm.models.conversation', CrmConversation::class);
        $messageModel = config('refineder-crm.models.message', CrmMessage::class);

        return $this->hasManyThrough(
            $messageModel,
            $conversationModel,
            'id',             // conversations.id
            'conversation_id', // messages.conversation_id
            'conversation_id', // deals.conversation_id
            'id',             // conversations.id
        );
    }

    // --- Scopes ---

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('stage', [DealStage::Won, DealStage::Lost]);
    }

    public function scopeWon($query)
    {
        return $query->where('stage', DealStage::Won);
    }

    public function scopeLost($query)
    {
        return $query->where('stage', DealStage::Lost);
    }

    public function scopeOfStage($query, DealStage $stage)
    {
        return $query->where('stage', $stage);
    }

    // --- Helpers ---

    public function hasConversation(): bool
    {
        return $this->conversation_id !== null;
    }

    public function isClosed(): bool
    {
        return $this->stage->isTerminal();
    }

    public function isOverdue(): bool
    {
        if (! $this->expected_close_date || $this->isClosed()) {
            return false;
        }

        return $this->expected_close_date->isPast();
    }

    public function markAsWon(): void
    {
        $this->update([
            'stage' => DealStage::Won,
            'closed_at' => now(),
        ]);
    }

    public function markAsLost(): void
    {
        $this->update([
            'stage' => DealStage::Lost,
            'closed_at' => now(),
        ]);
    }
}
