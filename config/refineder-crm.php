<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific CRM features. Set to false to hide
    | the resource from navigation and disable its functionality.
    |
    */
    'features' => [
        'contacts' => true,
        'conversations' => true,
        'deals' => true,
        'whatsapp_sessions' => true,
        'widgets' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Polling Interval
    |--------------------------------------------------------------------------
    |
    | How often (in seconds) the chat interface polls for new messages.
    | Lower values = more real-time, but more server load.
    |
    */
    'chat_poll_interval' => 5,

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        'group' => 'CRM',
        'icon' => 'heroicon-o-chat-bubble-left-right',
        'sort' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table Prefix
    |--------------------------------------------------------------------------
    |
    | All tables created by this plugin will use this prefix.
    |
    */
    'table_prefix' => 'crm_',

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    */
    'currency' => 'USD',

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhook' => [
        'prefix' => 'refineder-crm/webhook',
        'middleware' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */
    'max_media_size' => 16, // MB

    /*
    |--------------------------------------------------------------------------
    | Models (Overridable)
    |--------------------------------------------------------------------------
    |
    | You can override any model with your own by changing the class here.
    |
    */
    'models' => [
        'contact' => \Refineder\FilamentCrm\Models\CrmContact::class,
        'conversation' => \Refineder\FilamentCrm\Models\CrmConversation::class,
        'message' => \Refineder\FilamentCrm\Models\CrmMessage::class,
        'deal' => \Refineder\FilamentCrm\Models\CrmDeal::class,
        'deal_stage' => \Refineder\FilamentCrm\Models\CrmDealStage::class,
        'whatsapp_session' => \Refineder\FilamentCrm\Models\WhatsappSession::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Deal Stages (Default Pipeline)
    |--------------------------------------------------------------------------
    */
    'deal_stages' => [
        ['name' => 'Lead', 'color' => 'gray', 'order' => 1],
        ['name' => 'Qualified', 'color' => 'info', 'order' => 2],
        ['name' => 'Proposal', 'color' => 'warning', 'order' => 3],
        ['name' => 'Negotiation', 'color' => 'primary', 'order' => 4],
        ['name' => 'Won', 'color' => 'success', 'order' => 5, 'is_won' => true],
        ['name' => 'Lost', 'color' => 'danger', 'order' => 6, 'is_lost' => true],
    ],

];
