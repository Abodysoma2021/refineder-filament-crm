<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmContact extends Model
{
    protected $table = 'crm_contacts';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'last_message_at' => 'datetime',
        ];
    }

    // --- Relationships ---

    public function whatsappSession(): BelongsTo
    {
        return $this->belongsTo(config('refineder-crm.models.whatsapp_session'), 'whatsapp_session_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.conversation'), 'contact_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(config('refineder-crm.models.deal'), 'contact_id');
    }

    // --- Scopes ---

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('company', 'like', "%{$search}%");
        });
    }

    // --- Helpers ---

    public function getDisplayName(): string
    {
        return $this->name ?: $this->phone;
    }

    public function getInitials(): string
    {
        $name = $this->getDisplayName();
        $words = explode(' ', $name);

        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}
