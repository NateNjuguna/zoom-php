<?php

return [
    'from'  => env('MAIL_SENDER', env('MAIL_USERNAME')),
    'drivers'   => [
        'smtp'  => [
            'auth'          => env('SMTP_AUTH', false),
            'host'          => env('SMTP_HOST', 'localhost'),
            'password'      => env('SMTP_PASSWORD'),
            'port'          => env('SMTP_PORT'),
        ],
    ],
    'encryption'    => env('MAIL_ENCRYPTION', null),
    'username'      => env('MAIL_USERNAME'),
];
