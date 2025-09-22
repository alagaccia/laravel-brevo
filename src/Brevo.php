<?php

namespace AndreaLagaccia\Brevo;

use Illuminate\Support\Facades\Log;


class Brevo
{
    protected $api_key;
    protected $api_base_url;
    protected $api_headers;
    protected $setting_table_name;
    protected $setting_column_name;
    protected $setting_sms_counter_column_name;
    protected $setting_sms_counter_value_name;
    protected $sms_sender_name;
    protected $sms_webhook;
    protected $list_id;

    protected const API_BASE_URL = "https://api.brevo.com/v3/";
    public const VERSION = '0.40';

    public function __construct()
    {
        $this->set_api_key();
        $this->set_api_base_url();
        $this->set_api_headers();
        $this->set_list_id();
        $this->set_setting_table_name();
        $this->set_setting_column_name();
        $this->set_setting_sms_counter_column_name();
        $this->set_setting_sms_counter_value_name();
        $this->set_sms_sender_name();
        $this->set_sms_webhook();
    }

    public function set_api_key()
    {
        $this->api_key = config('brevo.API_KEY') ?? env('BREVO_API_KEY');

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting API key", ['api_key' => $this->api_key]);
        }
    }

    public function set_api_base_url()
    {
        $this->api_base_url = self::API_BASE_URL;

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting API base URL", ['api_base_url' => $this->api_base_url]);
        }
    }

    public function set_api_headers()
    {
        $this->api_headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Api-Key' => config('brevo.API_KEY'),
        ];

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting API headers", ['api_headers' => $this->api_headers]);
        }
    }

    public function get_api_headers()
    {
        return $this->api_headers;
    }

    public function set_setting_table_name()
    {
        $this->setting_table_name = config('brevo.SETTING_TABLE_NAME') ?? env('BREVO_SETTING_TABLE_NAME');

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting setting table name", ['setting_table_name' => $this->setting_table_name]);
        }
    }

    public function set_setting_column_name()
    {
        $this->setting_column_name = config('brevo.SETTING_COLUMN_NAME') ?? env('BREVO_SETTING_COLUMN_NAME');

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting setting column name", ['setting_column_name' => $this->setting_column_name]);
        }
    }

    public function set_setting_sms_counter_column_name()
    {
        $this->setting_sms_counter_column_name = config('brevo.SETTING_SMS_COUNTER_COLUMN_NAME') ?? env('BREVO_SETTINGS_SMS_COUNTER_COLUMN_NAME') ?? null;

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting setting SMS counter column name", ['setting_sms_counter_column_name' => $this->setting_sms_counter_column_name]);
        }
    }

    public function set_setting_sms_counter_value_name()
    {
        $this->setting_sms_counter_value_name = config('brevo.SETTING_SMS_COUNTER_VALUE_NAME') ?? env('BREVO_SETTINGS_SMS_COUNTER_VALUE_NAME') ?? null;

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting SMS counter value name", ['setting_sms_counter_value_name' => $this->setting_sms_counter_value_name]);
        }
    }

    public function set_sms_sender_name()
    {
        $this->sms_sender_name = config('brevo.SMS_SENDER_NAME') ?? env('BREVO_SMS_SENDER_NAME');

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting SMS sender name", ['sms_sender_name' => $this->sms_sender_name]);
        }
    }

    public function set_sms_webhook()
    {
        $this->sms_webhook = config('brevo.SMS_WEBHOOK') ?? env('BREVO_SMS_WEBHOOK') ?? null;

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting SMS webhook", ['sms_webhook' => $this->sms_webhook]);
        }
    }

    public function set_list_id()
    {
        $this->list_id = config('brevo.LIST_ID') ?? env('BREVO_LIST_ID');

        if (config('brevo.LOG_ENABLED')) {
            Log::info("Brevo: setting list ID", ['list_id' => $this->list_id]);
        }
    }

    public function get_list_id()
    {
        return $this->list_id;
    }
}
