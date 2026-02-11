<?php

declare(strict_types=1);

namespace Refineder\FilamentCrm\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Refineder\FilamentCrm\Enums\DealStage;
use Refineder\FilamentCrm\Models\CrmContact;
use Refineder\FilamentCrm\Models\CrmConversation;
use Refineder\FilamentCrm\Models\CrmDeal;
use Refineder\FilamentCrm\Models\CrmMessage;

class CrmStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    protected ?string $pollingInterval = '30s';

    /**
     * Listen for real-time refresh events dispatched by GlobalNotificationListener.
     *
     * @var array<string, string>
     */
    protected $listeners = [
        'crm-new-message' => '$refresh',
    ];

    protected function getStats(): array
    {
        $totalContacts = CrmContact::count();
        $unreadConversations = CrmConversation::unread()->count();
        $openDeals = CrmDeal::open()->count();
        $wonDealsValue = CrmDeal::won()
            ->sum('value');
        $todayMessages = CrmMessage::whereDate('created_at', today())->count();

        return [
            Stat::make(
                __('refineder-crm::widgets.stats.contacts'),
                $totalContacts
            )
                ->description(__('refineder-crm::widgets.stats.total_contacts'))
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make(
                __('refineder-crm::widgets.stats.unread'),
                $unreadConversations
            )
                ->description(__('refineder-crm::widgets.stats.unread_conversations'))
                ->descriptionIcon('heroicon-o-chat-bubble-left-ellipsis')
                ->color($unreadConversations > 0 ? 'danger' : 'success'),

            Stat::make(
                __('refineder-crm::widgets.stats.open_deals'),
                $openDeals
            )
                ->description(__('refineder-crm::widgets.stats.active_pipeline'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),

            Stat::make(
                __('refineder-crm::widgets.stats.revenue'),
                '$' . number_format((float) $wonDealsValue, 2)
            )
                ->description(__('refineder-crm::widgets.stats.won_deals_value'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make(
                __('refineder-crm::widgets.stats.today_messages'),
                $todayMessages
            )
                ->description(__('refineder-crm::widgets.stats.messages_today'))
                ->descriptionIcon('heroicon-o-envelope')
                ->color('info'),
        ];
    }
}
