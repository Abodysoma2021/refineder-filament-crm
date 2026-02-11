<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Refineder\FilamentCrm\Enums\MessageStatus;
use Refineder\FilamentCrm\Enums\MessageType;

class CrmMessage extends Model
{
    protected $table = 'crm_messages';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class,
            'status' => MessageStatus::class,
            'is_from_me' => 'boolean',
            'metadata' => 'array',
        ];
    }

    // --- Relationships ---

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.conversation'), 'conversation_id');
    }

    public function repliedTo(): BelongsTo
    {
        return $this->belongsTo(static::class, 'replied_to_id');
    }

    // --- Scopes ---

    public function scopeIncoming($query)
    {
        return $query->where('is_from_me', false);
    }

    public function scopeOutgoing($query)
    {
        return $query->where('is_from_me', true);
    }

    public function scopeOfType($query, MessageType $type)
    {
        return $query->where('type', $type);
    }

    // --- Helpers ---

    public function isMedia(): bool
    {
        return in_array($this->type, [
            MessageType::Image,
            MessageType::Video,
            MessageType::Audio,
            MessageType::Document,
            MessageType::Sticker,
        ]);
    }

    public function getPreview(): string
    {
        if ($this->body) {
            return mb_strlen($this->body) > 50
                ? mb_substr($this->body, 0, 50) . '...'
                : $this->body;
        }

        return $this->type->label();
    }
}
