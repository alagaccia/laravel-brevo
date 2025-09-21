<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default BREVO variables
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the list to use for syncing contacts
    | and the api key to connect to the api server of BREVO
    |
    */

    'API_KEY' => env('BREVO_API_KEY'),
    'LIST_ID' => env('BREVO_LIST_ID'),
    'SMS_SENDER_NAME' => env('BREVO_SMS_SENDER_NAME'),
    'SMS_WEBHOOK' => env('BREVO_SMS_WEBHOOK'),
    'SETTING_TABLE_NAME' => env('BREVO_SETTINGS_TABLE_NAME'),
    'SETTING_COLUMN_NAME' => env('BREVO_SETTINGS_COLUMN_NAME'),
    'SETTING_SMS_COUNTER_COLUMN_NAME' => env('BREVO_SETTINGS_SMS_COUNTER_COLUMN_NAME'),
    'SETTING_SMS_COUNTER_VALUE_NAME' => env('BREVO_SETTINGS_SMS_COUNTER_VALUE_NAME'),
];
