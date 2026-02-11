<?php

return [
    'navigation_label' => 'Deals',
    'model_label' => 'Deal',
    'plural_model_label' => 'Deals',

    'stages' => [
        'lead' => 'Lead',
        'qualified' => 'Qualified',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'won' => 'Won',
        'lost' => 'Lost',
    ],

    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],

    'form' => [
        'deal_info' => 'Deal Information',
        'status_info' => 'Status & Tracking',
        'title' => 'Title',
        'contact' => 'Contact',
        'value' => 'Value',
        'currency' => 'Currency',
        'stage' => 'Stage',
        'priority' => 'Priority',
        'expected_close_date' => 'Expected Close Date',
        'notes' => 'Notes',
    ],

    'table' => [
        'title' => 'Title',
        'contact' => 'Contact',
        'value' => 'Value',
        'stage' => 'Stage',
        'priority' => 'Priority',
        'expected_close' => 'Expected Close',
        'created_at' => 'Created',
    ],

    'filters' => [
        'stage' => 'Stage',
        'priority' => 'Priority',
    ],

    'actions' => [
        'change_stage' => 'Change Stage',
        'mark_won' => 'Mark as Won',
        'mark_lost' => 'Mark as Lost',
        'mark_won_confirmation' => 'Are you sure you want to mark this deal as won? This will close the deal.',
        'mark_lost_confirmation' => 'Are you sure you want to mark this deal as lost? This will close the deal.',
        'reopen' => 'Reopen Deal',
        'reopen_confirmation' => 'Are you sure you want to reopen this deal? It will be moved back to the Lead stage.',
    ],

    'notifications' => [
        'stage_changed' => 'Stage Updated',
        'stage_changed_to' => 'Deal moved to :stage stage.',
        'deal_won' => 'Deal Won!',
        'deal_lost' => 'Deal Lost',
        'deal_reopened' => 'Deal Reopened',
    ],

    'sidebar' => [
        'deal_info' => 'Deal Information',
        'contact_info' => 'Contact Information',
        'deal_history' => 'Deal History',
        'session' => 'WhatsApp Session',
        'no_conversation' => 'No conversation linked',
        'no_conversation_hint' => 'This deal does not have an active WhatsApp conversation yet.',
        'overdue' => 'Overdue',
        'closed_at' => 'Closed',
        'current' => 'Current',
        'view_deal' => 'Open Deal',
    ],
];
