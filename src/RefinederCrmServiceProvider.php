<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm;

use Livewire\Livewire;
use Refineder\FilamentCrm\Http\Controllers\WebhookController;
use Refineder\FilamentCrm\Livewire\ChatBox;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RefinederCrmServiceProvider extends PackageServiceProvider
{
    public static string $name = 'refineder-crm';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                'create_crm_whatsapp_sessions_table',
                'create_crm_contacts_table',
                'create_crm_conversations_table',
                'create_crm_messages_table',
                'create_crm_deal_stages_table',
                'create_crm_deals_table',
            ])
            ->hasRoutes('web');
    }

    public function packageBooted(): void
    {
        Livewire::component('refineder-crm-chat-box', ChatBox::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Services\WasenderService::class, function ($app) {
            return new Services\WasenderService();
        });
    }
}
