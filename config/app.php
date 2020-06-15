<?php

return [
    'locale'    => [
        // The application's default localization/language
        'default'   => env('APP_LOCALE_DEFAULT'),
        // The locale used by the app in case the default is left out
        'fallback'  => env('APP_LOCALE_FALLBACK'),
    ],
    'name'      => env('APP_NAME', 'App'),
    'namespace' => env('APP_NAMESPACE', '\App'),
    'url'       => env('APP_URL', 'http://locahost:9002/'),
];
