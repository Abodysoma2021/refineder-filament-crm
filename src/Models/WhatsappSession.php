<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Refineder\FilamentCrm\Enums\SessionStatus;

class WhatsappSession extends Model
{
    protected $table = 'crm_whatsapp_sessions';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'personal_access_token' => 'encrypted',
            'webhook_secret' => 'encrypted',
            'status' => SessionStatus::class,
            'is_default' => 'boolean',
            'metadata' => 'array',
        ];
    }

    // --- Relationships ---

    public function contacts(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.contact'), 'whatsapp_session_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.conversation'), 'whatsapp_session_id');
    }

    // --- Scopes ---

    public function scopeConnected($query)
    {
        return $query->where('status', SessionStatus::Connected);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // --- Helpers ---

    public function isConnected(): bool
    {
        return $this->status === SessionStatus::Connected;
    }

    public function getWebhookUrl(): string
    {
        $prefix = config('refineder-crm.webhook.prefix', 'refineder-crm/webhook');

        return url("{$prefix}/{$this->id}");
    }
}
