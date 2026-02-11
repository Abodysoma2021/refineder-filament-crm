<?php

return [
    'navigation_label' => 'الصفقات',
    'model_label' => 'صفقة',
    'plural_model_label' => 'الصفقات',

    'stages' => [
        'lead' => 'عميل محتمل',
        'qualified' => 'مؤهل',
        'proposal' => 'عرض سعر',
        'negotiation' => 'تفاوض',
        'won' => 'تم الفوز',
        'lost' => 'خسارة',
    ],

    'priorities' => [
        'low' => 'منخفضة',
        'medium' => 'متوسطة',
        'high' => 'عالية',
        'urgent' => 'عاجلة',
    ],

    'form' => [
        'deal_info' => 'معلومات الصفقة',
        'status_info' => 'الحالة والتتبع',
        'title' => 'العنوان',
        'contact' => 'جهة الاتصال',
        'value' => 'القيمة',
        'currency' => 'العملة',
        'stage' => 'المرحلة',
        'priority' => 'الأولوية',
        'expected_close_date' => 'تاريخ الإغلاق المتوقع',
        'notes' => 'ملاحظات',
    ],

    'table' => [
        'title' => 'العنوان',
        'contact' => 'جهة الاتصال',
        'value' => 'القيمة',
        'stage' => 'المرحلة',
        'priority' => 'الأولوية',
        'expected_close' => 'الإغلاق المتوقع',
        'created_at' => 'تاريخ الإنشاء',
    ],

    'filters' => [
        'stage' => 'المرحلة',
        'priority' => 'الأولوية',
    ],

    'actions' => [
        'change_stage' => 'تغيير المرحلة',
        'mark_won' => 'تحديد كفوز',
        'mark_lost' => 'تحديد كخسارة',
        'mark_won_confirmation' => 'هل أنت متأكد أنك تريد تحديد هذه الصفقة كفوز؟ سيتم إغلاق الصفقة.',
        'mark_lost_confirmation' => 'هل أنت متأكد أنك تريد تحديد هذه الصفقة كخسارة؟ سيتم إغلاق الصفقة.',
        'reopen' => 'إعادة فتح الصفقة',
        'reopen_confirmation' => 'هل أنت متأكد أنك تريد إعادة فتح هذه الصفقة؟ سيتم نقلها إلى مرحلة عميل محتمل.',
    ],

    'notifications' => [
        'stage_changed' => 'تم تحديث المرحلة',
        'stage_changed_to' => 'تم نقل الصفقة إلى مرحلة :stage.',
        'deal_won' => 'تم الفوز بالصفقة!',
        'deal_lost' => 'تم خسارة الصفقة',
        'deal_reopened' => 'تم إعادة فتح الصفقة',
    ],

    'sidebar' => [
        'deal_info' => 'معلومات الصفقة',
        'contact_info' => 'معلومات جهة الاتصال',
        'deal_history' => 'سجل الصفقات',
        'session' => 'جلسة واتساب',
        'no_conversation' => 'لا توجد محادثة مرتبطة',
        'no_conversation_hint' => 'هذه الصفقة ليس لديها محادثة واتساب نشطة بعد.',
        'overdue' => 'متأخر',
        'closed_at' => 'تاريخ الإغلاق',
        'current' => 'الحالية',
        'view_deal' => 'فتح الصفقة',
    ],
];
