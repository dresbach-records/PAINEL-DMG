<?php

return [
    'transport' => env('PCT_MAIL_TRANSPORT', 'smtp'), // smtp|sendmail
    'host' => env('PCT_MAIL_HOST', '127.0.0.1'),
    'port' => (int) env('PCT_MAIL_PORT', 25),
    'username' => env('PCT_MAIL_USERNAME'),
    'password' => env('PCT_MAIL_PASSWORD'),
    'encryption' => env('PCT_MAIL_ENCRYPTION', null),
    'from' => [
        'address' => env('PCT_MAIL_FROM_ADDRESS', 'nao-responder@pct.local'),
        'name' => env('PCT_MAIL_FROM_NAME', 'PCT Mailer'),
    ],
    'queue' => [
        'connection' => env('PCT_MAIL_QUEUE_CONNECTION', 'redis'),
        'name' => env('PCT_MAIL_QUEUE_NAME', 'pct-mail'),
    ],
];
