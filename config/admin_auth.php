<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Credentials (Code-Based)
    |--------------------------------------------------------------------------
    |
    | Add admins here directly. You can keep plain passwords for demo usage
    | or use bcrypt hashes.
    |
    */
    'admins' => [
        [
            'email' => env('ADMIN_EMAIL', 'abdulmoizakhter9@gmail.com'),
            'password' => env('ADMIN_PASSWORD', 'admin123'),
        ],
        [
            'email' => env('ADMIN_EMAIL', 'abdulmoizakhter8@gmail.com'),
            'password' => env('ADMIN_PASSWORD', 'admin123'),
        ],
        // Example:
        // ['email' => 'secondadmin@example.com', 'password' => 'secret123'],
    ],
];
