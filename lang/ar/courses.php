<?php

return [
    'title'    => 'المقررات',
    'subtitle' => 'إدارة المقررات الدراسية',
    'singular' => 'مقرر',

    'fields' => [
        'code'            => 'الرمز',
        'name'            => 'الاسم',
        'program'         => 'البرنامج',
        'credit_hours'    => 'الساعات المعتمدة',
        'level'           => 'المستوى',
        'semester_number' => 'الفصل الدراسي',
        'course_type'     => 'نوع المقرر',
        'description'     => 'الوصف',
        'is_active'       => 'الحالة',
    ],

    'types' => [
        'compulsory' => 'إجباري',
        'elective'   => 'اختياري',
    ],

    'statuses' => [
        'active'   => 'مفعّل',
        'inactive' => 'معطّل',
    ],

    'filters' => [
        'search'             => 'بحث',
        'search_placeholder' => 'رمز أو اسم المقرر...',
        'all'                => 'الكل',
    ],

    'actions' => [
        'create' => 'إضافة مقرر',
        'edit'   => 'تعديل المقرر',
        'view'   => 'عرض المقرر',
        'save'   => 'حفظ',
        'cancel' => 'إلغاء',
        'back'   => 'رجوع',
    ],

    'empty' => [
        'title'       => 'لا توجد مقررات',
        'description' => 'لم يتم إضافة أي مقرر بعد',
    ],

    'messages' => [
        'confirm_delete' => 'هل تريد حذف هذا المقرر؟',
    ],

    'show' => [
        'info'             => 'بيانات المقرر',
        'registrations'    => 'التسجيلات',
        'no_registrations' => 'لا توجد تسجيلات على هذا المقرر',
        'students_count'   => 'عدد الطلاب المسجلين',
    ],
];