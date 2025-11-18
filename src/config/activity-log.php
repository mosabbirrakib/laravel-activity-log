<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activity Log Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the table that will store activity logs.
    |
    */
    'table_name' => 'activity_logs',

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model that will be used to associate activity logs.
    |
    */
    'user_model' => env('ACTIVITY_LOG_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for the activity log API and views.
    |
    */
    'routes' => [
        'enabled' => true,
        'prefix' => 'activity-logs',
        'middleware' => ['web', 'auth'],
        'api_middleware' => ['api', 'auth:sanctum'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Default number of items per page for activity logs.
    |
    */
    'per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Date Format
    |--------------------------------------------------------------------------
    |
    | The date format used for displaying activity log timestamps.
    |
    */
    'date_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Automatic Logging
    |--------------------------------------------------------------------------
    |
    | Enable automatic logging for model events.
    |
    */
    'auto_log' => [
        'enabled' => false,
        'events' => ['created', 'updated', 'deleted'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Retention
    |--------------------------------------------------------------------------
    |
    | Number of days to keep activity logs. Set to null to keep forever.
    |
    */
    'retention_days' => null,

    /*
    |--------------------------------------------------------------------------
    | IP Address Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of user IP addresses.
    |
    */
    'log_ip_address' => true,

    /*
    |--------------------------------------------------------------------------
    | User Agent Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of user agent strings.
    |
    */
    'log_user_agent' => true,

    /*
    |--------------------------------------------------------------------------
    | Properties Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of additional properties/metadata.
    |
    */
    'log_properties' => true,

    /*
    |--------------------------------------------------------------------------
    | Excluded Attributes
    |--------------------------------------------------------------------------
    |
    | Attributes that should not be logged (e.g., passwords, tokens).
    |
    */
    'excluded_attributes' => [
        'password',
        'password_confirmation',
        'remember_token',
        'api_token',
        'token',
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the UI components.
    |
    */
    'ui' => [
        'theme' => 'tailwind', // 'tailwind' or 'bootstrap'
        'show_user_info' => true,
        'show_ip_address' => true,
        'show_user_agent' => false,
        'items_per_page_options' => [10, 15, 25, 50, 100],
    ],
];

