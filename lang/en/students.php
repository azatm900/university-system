<?php

return [
    'title'    => 'Students',
    'subtitle' => 'Manage students enrolled in the university',

    'fields' => [
        'student_number'  => 'Student No.',
        'national_id'     => 'National ID',
        'name'            => 'Name',
        'email'           => 'Email',
        'program'         => 'Program',
        'user'            => 'User Account',
        'date_of_birth'   => 'Date of Birth',
        'gender'          => 'Gender',
        'phone'           => 'Phone',
        'address'         => 'Address',
        'enrollment_date' => 'Enrollment Date',
        'status'          => 'Status',
        'actions'         => 'Actions',
    ],

    'actions' => [
        'create'          => 'New Student',
        'view'            => 'View',
        'edit'            => 'Edit',
        'delete'          => 'Delete',
        'restore'         => 'Restore',
        'force_delete'    => 'Force Delete',
        'save'            => 'Save',
        'cancel'          => 'Cancel',
        'back'            => 'Back',
        'confirm_delete'  => 'Delete this student?',
        'confirm_restore' => 'Restore this student?',
        'confirm_force'   => 'Force delete? This cannot be undone.',
    ],

    'filters' => [
        'search'             => 'Search',
        'search_placeholder' => 'Name / Student No. / National ID',
        'all_programs'       => 'All Programs',
        'all_statuses'       => 'All Statuses',
        'apply'              => 'Apply',
        'reset'              => 'Reset',
    ],

    'statuses' => [
        'active'    => 'Active',
        'graduated' => 'Graduated',
        'suspended' => 'Suspended',
        'withdrawn' => 'Withdrawn',
    ],

    'genders' => [
        'male'   => 'Male',
        'female' => 'Female',
    ],

    'empty' => [
        'title'       => 'No students',
        'description' => 'No students matched the current search or filters.',
    ],

    'messages' => [
        'created'  => 'Student created successfully.',
        'updated'  => 'Student updated.',
        'deleted'  => 'Student deleted (restorable).',
        'restored' => 'Student restored.',
        'forced'   => 'Student permanently deleted.',
    ],

    'show' => [
        'details'       => 'Details',
        'registrations' => 'Registrations',
        'invoices'      => 'Invoices',
        'results'       => 'Results',
        'personal'      => 'Personal Info',
        'academic'      => 'Academic Info',
        'contact'       => 'Contact Info',
        'deleted_badge' => 'Deleted (restorable)',
    ],

    'stats' => [
        'registrations' => 'Registrations',
        'invoices'      => 'Invoices',
        'results'       => 'Results',
    ],
];