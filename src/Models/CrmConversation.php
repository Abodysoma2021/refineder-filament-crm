<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Refineder\FilamentCrm\Enums\ConversationStatus;

class CrmConversation extends Model
{
    protected $table = 'crm_conversations';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => ConversationStatus::class,
            'last_message_at' => 'datetime',
            'is_archived' => 'boolean',
        ];
    }

    // --- Relationships ---

    public function contact(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.contact'), 'contact_id');
    }

    public function whatsappSession(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.whatsapp_session'), 'whatsapp_session_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.message'), 'conversation_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.deal'), 'conversation_id');
    }

    // --- Scopes ---

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', ConversationStatus::Open);
    }

    public function scopeUnread($query)
    {
        return $query->where('unread_count', '>', 0);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    // --- Helpers ---

    public function markAsRead(): void
    {
        $this->update(['unread_count' => 0]);
    }

    public function incrementUnread(): void
    {
        $this->increment('unread_count');
    }

    public function archive(): void
    {
        $this->update(['is_archived' => true, 'status' => ConversationStatus::Closed]);
    }
}
