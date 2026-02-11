<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Refineder\FilamentCrm\Livewire\ChatBox;
use Refineder\FilamentCrm\Resources\ContactResource\ContactResource;
use Refineder\FilamentCrm\Resources\ConversationResource\ConversationResource;
use Refineder\FilamentCrm\Resources\DealResource\DealResource;
use Refineder\FilamentCrm\Resources\WhatsappSessionResource\WhatsappSessionResource;
use Refineder\FilamentCrm\Widgets\CrmStatsWidget;
use Refineder\FilamentCrm\Widgets\RecentConversationsWidget;

class RefinederCrmPlugin implements Plugin
{
    protected bool $hasContacts = true;

    protected bool $hasConversations = true;

    protected bool $hasDeals = true;

    protected bool $hasWhatsappSessions = true;

    protected bool $hasWidgets = true;

    protected string $navigationGroup = 'CRM';

    protected int $navigationSort = 1;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'refineder-crm';
    }

    public function register(Panel $panel): void
    {
        $resources = [];
        $widgets = [];
        $pages = [];

        if ($this->hasContacts) {
            $resources[] = ContactResource::class;
        }

        if ($this->hasConversations) {
            $resources[] = ConversationResource::class;
        }

        if ($this->hasDeals) {
            $resources[] = DealResource::class;
        }

        if ($this->hasWhatsappSessions) {
            $resources[] = WhatsappSessionResource::class;
        }

        if ($this->hasWidgets) {
            $widgets[] = CrmStatsWidget::class;
            $widgets[] = RecentConversationsWidget::class;
        }

        $panel
            ->resources($resources)
            ->widgets($widgets)
            ->pages($pages)
            ->livewireComponents([
                ChatBox::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    // --- Configuration Methods ---

    public function contacts(bool $condition = true): static
    {
        $this->hasContacts = $condition;

        return $this;
    }

    public function hasContacts(): bool
    {
        return $this->hasContacts;
    }

    public function conversations(bool $condition = true): static
    {
        $this->hasConversations = $condition;

        return $this;
    }

    public function hasConversations(): bool
    {
        return $this->hasConversations;
    }

    public function deals(bool $condition = true): static
    {
        $this->hasDeals = $condition;

        return $this;
    }

    public function hasDeals(): bool
    {
        return $this->hasDeals;
    }

    public function whatsappSessions(bool $condition = true): static
    {
        $this->hasWhatsappSessions = $condition;

        return $this;
    }

    public function hasWhatsappSessions(): bool
    {
        return $this->hasWhatsappSessions;
    }

    public function widgets(bool $condition = true): static
    {
        $this->hasWidgets = $condition;

        return $this;
    }

    public function hasWidgets(): bool
    {
        return $this->hasWidgets;
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): string
    {
        return $this->navigationGroup;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): int
    {
        return $this->navigationSort;
    }
}
