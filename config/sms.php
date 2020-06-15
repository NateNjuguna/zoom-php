<?php

use Zoom\SMS\Drivers\AfricasTalking;
use Zoom\SMS\Drivers\Plivo;
use Zoom\SMS\Drivers\Textlocal;
use Zoom\SMS\Drivers\Twilio;

return [

    'driver'    => env('SMS_DRIVER', 'africastalking'),
    'drivers'   => [
        'africastalking'    => [
            'class'     => AfricasTalking::class,
            'settings'  => [
                'API_KEY'   => env('AFT_API_KEY', 'aft_api_key'),
                'SENDER_ID' => env('AFT_SENDER_ID', 'aft_sender_id'),
                'USERNAME'  => env('AFT_USERNAME', 'aft_username'),
            ],
        ],
        'plivo'             => [
            'class'     => Plivo::class,
            'settings'  => [
                'AUTH_ID'       => env('PLIVO_AUTH_ID', 'plivo_auth_id'),
                'AUTH_TOKEN'    => env('PLIVO_AUTH_TOKEN', 'plivo_auth_token'),
                'CALLBACK_URL'  => env('PLIVO_CALLBACK_URL', 'plivo_callback_url'),
                'NUMBER'        => env('PLIVO_NUMBER', 'plivo_number'),
            ],
        ],
        'textLocal'         => [
            'class'     => Textlocal::class,
            'settings'  => [
                'API_KEY'       => env('TEXTLOCAL_API_KEY', 'textlocal_api_key'),
                'RECEIPT_URL'   => env('TEXTLOCAL_RECEIPT_URL', 'textlocal_receipt_url'),
                'SENDER'        => env('TEXTLOCAL_SENDER', 'textlocal_sender'),
            ],
        ],
        'twilio'            => [
            'class'     => Twilio::class,
            'settings'  => [
                'AUTH_TOKEN'        => env('TWILIO_AUTH_TOKEN', 'twilio_auth_token'),
                'NUMBER'            => env('TWILIO_NUMBER', 'twilio_number'),
                'SID'               => env('TWILIO_SID', 'twilio_sid'),
                'STATUS_CALLBACK'   => env('TWILIO_STATUS_CALLBACK', 'twilio_status_callback'),
            ],
        ],
    ],

];
