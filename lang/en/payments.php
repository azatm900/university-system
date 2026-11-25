<?php

return [
    'title'    => 'Payments',
    'subtitle' => 'Manage invoice payments',
    'singular' => 'Payment',

    'fields' => [
        'invoice'          => 'Invoice',
        'student'          => 'Student',
        'amount'           => 'Amount',
        'payment_date'     => 'Payment Date',
        'method'           => 'Method',
        'reference_number' => 'Reference #',
        'receiver'         => 'Received By',
        'notes'            => 'Notes',
    ],

    'methods' => [
        'cash'          => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'card'          => 'Card',
        'check'         => 'Check',
    ],

    'filters' => [
        'search'             => 'Search',
        'search_placeholder' => 'Invoice # or reference...',
        'all'                => 'All',
    ],

    'actions' => [
        'create' => 'Add Payment',
        'edit'   => 'Edit Payment',
        'view'   => 'View Payment',
        'save'   => 'Save',
        'cancel' => 'Cancel',
        'back'   => 'Back',
    ],

    'empty' => [
        'title'       => 'No Payments',
        'description' => 'No payments have been recorded yet',
    ],

    'messages' => [
        'confirm_delete' => 'Are you sure you want to delete this payment?',
        'edit_note'      => 'Amount and method cannot be changed. Delete and re-record if needed.',
    ],
];