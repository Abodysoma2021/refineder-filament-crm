<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm;

use Illuminate\Support\Facades\Broadcast;
use Livewire\Livewire;
use Refineder\FilamentCrm\Http\Controllers\WebhookController;
use Refineder\FilamentCrm\Livewire\ChatBox;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Models\WhatsappSession;
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

        $this->registerBroadcastChannels();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Services\WasenderService::class, function ($app) {
            return new Services\WasenderService();
        });

        $this->app->singleton(Services\DealService::class, function ($app) {
            return new Services\DealService();
        });
    }

    /**
     * Register broadcast channel authorization callbacks.
     */
    protected function registerBroadcastChannels(): void
    {
        Broadcast::channel('crm.conversation.{conversationId}', function ($user, int $conversationId) {
            $conversation = CrmConversation::find($conversationId);

            return $conversation && $conversation->user_id === $user->id;
        });

        Broadcast::channel('crm.session.{sessionId}', function ($user, int $sessionId) {
            $session = WhatsappSession::find($sessionId);

            return $session && $session->user_id === $user->id;
        });
    }
}
