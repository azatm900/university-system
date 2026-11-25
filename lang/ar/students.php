<?php

return [
    'title'    => 'الطلاب',
    'subtitle' => 'إدارة بيانات الطلاب المسجلين في الجامعة',

    'fields' => [
        'student_number'  => 'الرقم الجامعي',
        'national_id'     => 'الرقم الوطني',
        'name'            => 'الاسم',
        'email'           => 'البريد الإلكتروني',
        'program'         => 'البرنامج',
        'user'            => 'حساب المستخدم',
        'date_of_birth'   => 'تاريخ الميلاد',
        'gender'          => 'الجنس',
        'phone'           => 'الهاتف',
        'address'         => 'العنوان',
        'enrollment_date' => 'تاريخ التسجيل',
        'status'          => 'الحالة',
        'actions'         => 'إجراءات',
    ],

    'actions' => [
        'create'          => 'طالب جديد',
        'view'            => 'عرض',
        'edit'            => 'تعديل',
        'delete'          => 'حذف',
        'restore'         => 'استعادة',
        'force_delete'    => 'حذف نهائي',
        'save'            => 'حفظ',
        'cancel'          => 'إلغاء',
        'back'            => 'رجوع',
        'confirm_delete'  => 'هل تريد حذف هذا الطالب؟',
        'confirm_restore' => 'هل تريد استعادة هذا الطالب؟',
        'confirm_force'   => 'هل تريد الحذف النهائي؟ لا يمكن التراجع.',
    ],

    'filters' => [
        'search'             => 'بحث',
        'search_placeholder' => 'الاسم / الرقم الجامعي / الرقم الوطني',
        'all_programs'       => 'كل البرامج',
        'all_statuses'       => 'كل الحالات',
        'apply'              => 'تطبيق',
        'reset'              => 'إعادة تعيين',
    ],

    'statuses' => [
        'active'    => 'نشط',
        'graduated' => 'متخرج',
        'suspended' => 'موقوف',
        'withdrawn' => 'منسحب',
    ],

    'genders' => [
        'male'   => 'ذكر',
        'female' => 'أنثى',
    ],

    'empty' => [
        'title'       => 'لا يوجد طلاب',
        'description' => 'لم يتم العثور على طلاب مطابقين للبحث أو الفلاتر الحالية.',
    ],

    'messages' => [
        'created'  => 'تم إنشاء الطالب بنجاح.',
        'updated'  => 'تم تحديث بيانات الطالب.',
        'deleted'  => 'تم حذف الطالب (قابل للاستعادة).',
        'restored' => 'تم استعادة الطالب.',
        'forced'   => 'تم الحذف النهائي للطالب.',
    ],

    'show' => [
        'details'       => 'البيانات',
        'registrations' => 'التسجيلات',
        'invoices'      => 'الفواتير',
        'results'       => 'النتائج',
        'personal'      => 'بيانات شخصية',
        'academic'      => 'بيانات أكاديمية',
        'contact'       => 'بيانات تواصل',
        'deleted_badge' => 'محذوف (قابل للاستعادة)',
    ],

    'stats' => [
        'registrations' => 'التسجيلات',
        'invoices'      => 'الفواتير',
        'results'       => 'النتائج',
    ],
];