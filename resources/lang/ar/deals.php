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
];
