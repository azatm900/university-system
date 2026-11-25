<?php

return [
    'index' => [
        'title'    => 'Users',
        'subtitle' => 'Manage user accounts and roles',
    ],
    'create' => [
        'title'    => 'New User',
        'subtitle' => 'Create a new user account',
    ],
    'edit' => [
        'title'    => 'Edit User',
        'subtitle' => 'Update user information',
    ],
    'show' => [
        'title' => 'User Details',
    ],
    'reset_password' => [
        'title'    => 'Reset Password',
        'warning'  => 'The user\'s current password will be replaced immediately.',
        'confirm'  => 'Confirm password reset?',
        'submit'   => 'Reset Password',
    ],
    'fields' => [
        'name'              => 'Name',
        'email'             => 'Email',
        'password'          => 'Password',
        'password_optional' => 'Password (leave blank to keep current)',
        'new_password'      => 'New Password',
        'confirm_password'  => 'Confirm Password',
        'role'              => 'Role',
        'status'            => 'Status',
        'is_active'         => 'Account is active',
        'created_at'        => 'Created At',
        'updated_at'        => 'Last Updated',
        'actions'           => 'Actions',
    ],
    'filters' => [
        'search'     => 'Search',
        'search_ph'  => 'Name or email...',
        'role'       => 'Role',
        'all_roles'  => 'All Roles',
        'status'     => 'Status',
        'all_status' => 'All',
        'apply'      => 'Apply',
        'reset'      => 'Clear',
    ],
    'hints' => [
        'password'          => 'At least 8 characters',
        'password_optional' => 'Leave blank to keep current password',
        'select_role'       => 'Select a role',
    ],
    'status' => [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ],
    'actions' => [
        'create'         => 'New User',
        'view'           => 'View',
        'edit'           => 'Edit',
        'toggle'         => 'Toggle Status',
        'delete'         => 'Delete',
        'reset_password' => 'Reset Password',
        'save'           => 'Save',
        'cancel'         => 'Cancel',
        'back'           => 'Back',
        'activate'       => 'Activate',
        'deactivate'     => 'Deactivate',
    ],
    'empty' => [
        'title'       => 'No users yet',
        'description' => 'Start by creating a new user.',
    ],
    'messages' => [
        'confirm_delete' => 'Delete this user?',
    ],
];