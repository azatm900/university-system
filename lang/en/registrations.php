<?php

return [
    'title'    => 'Registrations',
    'subtitle' => 'Manage student registrations',
    'singular' => 'Registration',

    'fields' => [
        'student'       => 'Student',
        'course'        => 'Course',
        'semester'      => 'Semester',
        'registered_at' => 'Registration Date',
        'status'        => 'Status',
        'result'        => 'Result',
    ],

    'statuses' => [
        'registered' => 'Registered',
        'dropped'    => 'Dropped',
        'completed'  => 'Completed',
    ],

    'filters' => [
        'all' => 'All',
    ],

    'actions' => [
        'create' => 'Add Registration',
        'edit'   => 'Edit Registration',
        'view'   => 'View Registration',
        'save'   => 'Save',
        'cancel' => 'Cancel',
        'back'   => 'Back',
    ],

    'empty' => [
        'title'       => 'No Registrations',
        'description' => 'No registrations have been added yet',
    ],

    'messages' => [
        'confirm_delete' => 'Are you sure you want to delete this registration?',
    ],

    'show' => [
        'info'      => 'Registration Information',
        'result'    => 'Course Result',
        'no_result' => 'No grade has been recorded yet',
    ],
];