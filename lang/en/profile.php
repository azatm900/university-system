<?php

return [
    'title' => 'My Profile',

    'edit' => [
        'title'               => 'My Profile',
        'subtitle'            => 'Manage your account information and password.',
        'account_info'        => 'Account Information',
        'change_password_cta' => 'Change Password',
    ],

    'password' => [
        'title'      => 'Change Password',
        'subtitle'   => 'Ensure your account uses a strong, unique password.',
        'form_title' => 'Update Password',
    ],

    'fields' => [
        'name'             => 'Name',
        'email'            => 'Email',
        'current_password' => 'Current Password',
        'new_password'     => 'New Password',
        'confirm_password' => 'Confirm New Password',
    ],

    'placeholders' => [
        'current_password' => 'Enter your current password',
        'new_password'     => 'At least 8 characters',
        'confirm_password' => 'Re-enter new password',
    ],

    'actions' => [
        'save'            => 'Save Changes',
        'cancel'          => 'Cancel',
        'update_password' => 'Update Password',
    ],

    'messages' => [
        'updated'          => 'Profile updated successfully.',
        'password_updated' => 'Password changed successfully.',
    ],
];