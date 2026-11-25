<?php

return [
    'title'    => 'الدفعات',
    'subtitle' => 'إدارة دفعات الفواتير',
    'singular' => 'دفعة',

    'fields' => [
        'invoice'          => 'الفاتورة',
        'student'          => 'الطالب',
        'amount'           => 'المبلغ',
        'payment_date'     => 'تاريخ الدفع',
        'method'           => 'طريقة الدفع',
        'reference_number' => 'رقم المرجع',
        'receiver'         => 'المستلم',
        'notes'            => 'ملاحظات',
    ],

    'methods' => [
        'cash'          => 'نقدي',
        'bank_transfer' => 'حوالة بنكية',
        'card'          => 'بطاقة',
        'check'         => 'شيك',
    ],

    'filters' => [
        'search'             => 'بحث',
        'search_placeholder' => 'رقم الفاتورة أو المرجع...',
        'all'                => 'الكل',
    ],

    'actions' => [
        'create' => 'تسجيل دفعة',
        'edit'   => 'تعديل الدفعة',
        'view'   => 'عرض الدفعة',
        'save'   => 'حفظ',
        'cancel' => 'إلغاء',
        'back'   => 'رجوع',
    ],

    'empty' => [
        'title'       => 'لا توجد دفعات',
        'description' => 'لم يتم تسجيل أي دفعة بعد',
    ],

    'messages' => [
        'confirm_delete' => 'هل تريد حذف هذه الدفعة؟',
        'edit_note'      => 'لا يمكن تعديل المبلغ أو الطريقة. احذف الدفعة وأعد تسجيلها إن لزم.',
    ],
];