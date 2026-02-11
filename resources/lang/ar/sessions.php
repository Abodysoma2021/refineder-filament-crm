<?php

return [
    'navigation_label' => 'جلسات واتساب',
    'model_label' => 'جلسة واتساب',
    'plural_model_label' => 'جلسات واتساب',

    'statuses' => [
        'connected' => 'متصل',
        'disconnected' => 'غير متصل',
        'connecting' => 'جاري الاتصال',
        'qr_pending' => 'بانتظار QR',
    ],

    'form' => [
        'session_details' => 'تفاصيل الجلسة',
        'session_details_description' => 'المعلومات الأساسية لجلسة واتساب.',
        'name' => 'اسم الجلسة',
        'phone_number' => 'رقم الهاتف',
        'is_default' => 'الجلسة الافتراضية',
        'is_default_help' => 'استخدم هذه الجلسة كافتراضية لإرسال الرسائل.',
        'api_configuration' => 'إعدادات API',
        'api_configuration_description' => 'أدخل بيانات WasenderAPI لهذه الجلسة.',
        'session_id' => 'معرف الجلسة في WasenderAPI',
        'session_id_help' => 'معرف الجلسة من لوحة تحكم WasenderAPI.',
        'api_key' => 'مفتاح API',
        'api_key_help' => 'مفتاح API الفريد لهذه الجلسة.',
        'personal_access_token' => 'رمز الوصول الشخصي',
        'personal_access_token_help' => 'اختياري. يُستخدم لعمليات إدارة الجلسات.',
        'webhook_configuration' => 'إعدادات Webhook',
        'webhook_configuration_description' => 'إعداد كيفية إرسال WasenderAPI للأحداث إلى تطبيقك.',
        'webhook_url' => 'رابط Webhook',
        'webhook_url_after_save' => 'سيتوفر رابط Webhook بعد الحفظ.',
        'webhook_secret' => 'سر Webhook',
        'webhook_secret_help' => 'أدخل نفس السر المُعد في إعدادات جلسة WasenderAPI.',
    ],

    'table' => [
        'name' => 'الاسم',
        'phone_number' => 'الهاتف',
        'status' => 'الحالة',
        'is_default' => 'افتراضي',
        'conversations' => 'المحادثات',
        'created_at' => 'تاريخ الإنشاء',
    ],

    'actions' => [
        'connect' => 'اتصال',
        'disconnect' => 'قطع الاتصال',
        'get_qr' => 'الحصول على رمز QR',
        'refresh_status' => 'تحديث الحالة',
    ],

    'notifications' => [
        'connecting' => 'جاري الاتصال بالجلسة...',
        'connect_failed' => 'فشل الاتصال بالجلسة.',
        'disconnected' => 'تم قطع الاتصال بنجاح.',
        'disconnect_failed' => 'فشل قطع الاتصال.',
    ],
];
