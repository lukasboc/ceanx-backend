<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'login',
        'logout',
        'register',
        'user/password',
        'forgot-password',
        'reset-password',
        'sanctum/csrf-cookie',
        'user/profile-information',
        'email/verification-notification',
        'user',
        'project/v1/projects',
        'project/v1/project_by_code',
        'project/v1/fix_entries_by_project_id',
        'project/v1/time_entries_by_project_id',
        'cost_estimations/all',
        'cost_estimation_positions',
        'cost_estimation_positions/new'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000', 'https://dev.simpleworklog.de'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
