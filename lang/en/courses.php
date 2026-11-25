<?php

return [
    'title'    => 'Courses',
    'subtitle' => 'Manage academic courses',
    'singular' => 'Course',

    'fields' => [
        'code'            => 'Code',
        'name'            => 'Name',
        'program'         => 'Program',
        'credit_hours'    => 'Credit Hours',
        'level'           => 'Level',
        'semester_number' => 'Semester',
        'course_type'     => 'Course Type',
        'description'     => 'Description',
        'is_active'       => 'Status',
    ],

    'types' => [
        'compulsory' => 'Compulsory',
        'elective'   => 'Elective',
    ],

    'statuses' => [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ],

    'filters' => [
        'search'             => 'Search',
        'search_placeholder' => 'Code or name...',
        'all'                => 'All',
    ],

    'actions' => [
        'create' => 'Add Course',
        'edit'   => 'Edit Course',
        'view'   => 'View Course',
        'save'   => 'Save',
        'cancel' => 'Cancel',
        'back'   => 'Back',
    ],

    'empty' => [
        'title'       => 'No Courses',
        'description' => 'No courses have been added yet',
    ],

    'messages' => [
        'confirm_delete' => 'Are you sure you want to delete this course?',
    ],

    'show' => [
        'info'             => 'Course Information',
        'registrations'    => 'Registrations',
        'no_registrations' => 'No registrations for this course',
        'students_count'   => 'Enrolled Students',
    ],
];