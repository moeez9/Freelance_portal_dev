<?php

return [
    'manual_admin' => [
        'name' => env('MANUAL_PAYMENT_ADMIN_NAME', 'Abdul Moiz Akhter'),
        'email' => env('MANUAL_PAYMENT_ADMIN_EMAIL', 'abdulmoizakhter9@gmail.com'),
        'easypaisa_number' => env('MANUAL_PAYMENT_EASYPAISA', '03437590221'),
    ],
    'methods' => ['credit_card', 'debit_card', 'jazzcash', 'easypaisa'],
];
