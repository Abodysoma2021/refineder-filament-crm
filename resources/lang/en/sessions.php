<?php

return [
    'navigation_label' => 'WhatsApp Sessions',
    'model_label' => 'WhatsApp Session',
    'plural_model_label' => 'WhatsApp Sessions',

    'statuses' => [
        'connected' => 'Connected',
        'disconnected' => 'Disconnected',
        'connecting' => 'Connecting',
        'qr_pending' => 'QR Pending',
    ],

    'form' => [
        'session_details' => 'Session Details',
        'session_details_description' => 'Basic information about your WhatsApp session.',
        'name' => 'Session Name',
        'phone_number' => 'Phone Number',
        'is_default' => 'Default Session',
        'is_default_help' => 'Use this session as the default for sending messages.',
        'api_configuration' => 'API Configuration',
        'api_configuration_description' => 'Enter your WasenderAPI credentials for this session.',
        'session_id' => 'WasenderAPI Session ID',
        'session_id_help' => 'The session ID from your WasenderAPI dashboard.',
        'api_key' => 'API Key',
        'api_key_help' => 'The unique API key for this session. Found in your WasenderAPI dashboard.',
        'personal_access_token' => 'Personal Access Token',
        'personal_access_token_help' => 'Optional. Used for session management operations.',
        'webhook_configuration' => 'Webhook Configuration',
        'webhook_configuration_description' => 'Configure how WasenderAPI sends events to your application.',
        'webhook_url' => 'Webhook URL',
        'webhook_url_after_save' => 'Webhook URL will be available after saving.',
        'webhook_secret' => 'Webhook Secret',
        'webhook_secret_help' => 'Enter the same secret configured in your WasenderAPI session settings.',
    ],

    'table' => [
        'name' => 'Name',
        'phone_number' => 'Phone',
        'status' => 'Status',
        'is_default' => 'Default',
        'conversations' => 'Conversations',
        'created_at' => 'Created',
    ],

    'actions' => [
        'connect' => 'Connect',
        'disconnect' => 'Disconnect',
        'get_qr' => 'Get QR Code',
        'refresh_status' => 'Refresh Status',
    ],

    'notifications' => [
        'connecting' => 'Session is connecting...',
        'connected' => 'Session connected successfully!',
        'connect_failed' => 'Failed to connect session.',
        'disconnected' => 'Session disconnected successfully.',
        'disconnect_failed' => 'Failed to disconnect session.',
        'status_synced' => 'Session status synced.',
        'current_status' => 'Current status: :status',
        'sync_failed' => 'Failed to sync session status.',
    ],
];
