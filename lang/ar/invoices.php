<?php

return [
    'title'    => 'الفواتير',
    'subtitle' => 'إدارة فواتير الطلاب',
    'singular' => 'فاتورة',

    'fields' => [
        'invoice_number' => 'رقم الفاتورة',
        'student'        => 'الطالب',
        'semester'       => 'الفصل الدراسي',
        'description'    => 'الوصف',
        'amount'         => 'المبلغ',
        'discount'       => 'الخصم',
        'total'          => 'الإجمالي',
        'paid_amount'    => 'المدفوع',
        'remaining'      => 'المتبقي',
        'status'         => 'الحالة',
        'due_date'       => 'تاريخ الاستحقاق',
    ],

    'statuses' => [
        'unpaid'    => 'غير مدفوعة',
        'partial'   => 'مدفوعة جزئياً',
        'paid'      => 'مدفوعة',
        'cancelled' => 'ملغاة',
    ],

    'filters' => [
        'search'             => 'بحث',
        'search_placeholder' => 'رقم الفاتورة أو اسم الطالب...',
        'all'                => 'الكل',
    ],

    'actions' => [
        'create'      => 'إضافة فاتورة',
        'edit'        => 'تعديل الفاتورة',
        'view'        => 'عرض الفاتورة',
        'save'        => 'حفظ',
        'cancel'      => 'إلغاء',
        'back'        => 'رجوع',
        'add_payment' => 'تسجيل دفعة',
    ],

    'empty' => [
        'title'       => 'لا توجد فواتير',
        'description' => 'لم يتم إضافة أي فاتورة بعد',
    ],

    'messages' => [
        'confirm_delete' => 'هل تريد حذف هذه الفاتورة؟',
    ],

    'show' => [
        'info'        => 'بيانات الفاتورة',
        'payments'    => 'الدفعات',
        'no_payments' => 'لا توجد دفعات على هذه الفاتورة',
    ],
];