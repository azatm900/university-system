<?php

return [
    'title'    => 'Invoices',
    'subtitle' => 'Manage student invoices',
    'singular' => 'Invoice',

    'fields' => [
        'invoice_number' => 'Invoice #',
        'student'        => 'Student',
        'semester'       => 'Semester',
        'description'    => 'Description',
        'amount'         => 'Amount',
        'discount'       => 'Discount',
        'total'          => 'Total',
        'paid_amount'    => 'Paid',
        'remaining'      => 'Remaining',
        'status'         => 'Status',
        'due_date'       => 'Due Date',
    ],

    'statuses' => [
        'unpaid'    => 'Unpaid',
        'partial'   => 'Partial',
        'paid'      => 'Paid',
        'cancelled' => 'Cancelled',
    ],

    'filters' => [
        'search'             => 'Search',
        'search_placeholder' => 'Invoice # or student name...',
        'all'                => 'All',
    ],

    'actions' => [
        'create'      => 'Add Invoice',
        'edit'        => 'Edit Invoice',
        'view'        => 'View Invoice',
        'save'        => 'Save',
        'cancel'      => 'Cancel',
        'back'        => 'Back',
        'add_payment' => 'Add Payment',
    ],

    'empty' => [
        'title'       => 'No Invoices',
        'description' => 'No invoices have been added yet',
    ],

    'messages' => [
        'confirm_delete' => 'Are you sure you want to delete this invoice?',
    ],

    'show' => [
        'info'        => 'Invoice Information',
        'payments'    => 'Payments',
        'no_payments' => 'No payments for this invoice',
    ],
];