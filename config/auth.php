<?php

return array(
    'locale'    => env('DB_LOCALE_COLUMN', 'locale'),
    'remember'  => env('DB_REMEMBER_COLUMN', 'remember_token'),
    'secret'    => env('APP_KEY'),
    'session'   => 'user',
    'table'     => env('DB_USER_TABLE', 'users'),
);