<?php

return [
    'title'    => 'التسجيلات',
    'subtitle' => 'إدارة تسجيلات الطلاب',
    'singular' => 'تسجيل',

    'fields' => [
        'student'       => 'الطالب',
        'course'        => 'المقرر',
        'semester'      => 'الفصل الدراسي',
        'registered_at' => 'تاريخ التسجيل',
        'status'        => 'الحالة',
        'result'        => 'الدرجة',
    ],

    'statuses' => [
        'registered' => 'مسجل',
        'dropped'    => 'مسحوب',
        'completed'  => 'مكتمل',
    ],

    'filters' => [
        'all' => 'الكل',
    ],

    'actions' => [
        'create' => 'إضافة تسجيل',
        'edit'   => 'تعديل التسجيل',
        'view'   => 'عرض التسجيل',
        'save'   => 'حفظ',
        'cancel' => 'إلغاء',
        'back'   => 'رجوع',
    ],

    'empty' => [
        'title'       => 'لا توجد تسجيلات',
        'description' => 'لم يتم إضافة أي تسجيل بعد',
    ],

    'messages' => [
        'confirm_delete' => 'هل تريد حذف هذا التسجيل؟',
    ],

    'show' => [
        'info'      => 'بيانات التسجيل',
        'result'    => 'نتيجة المقرر',
        'no_result' => 'لم تُسجل درجة بعد',
    ],
];