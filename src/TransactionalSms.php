<?php

namespace AndreaLagaccia\Brevo;

use AndreaLagaccia\Brevo\Brevo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TransactionalSms extends Brevo
{
    protected $url;

    public function __construct()
    {
        parent::__construct();

        $this->url = $this->api_base_url . 'transactionalSMS/sms';
    }

    public function send($number, $content, $tag = null)
    {
        if (config('brevo.LOG_ENABLED')) {
            Log::info("Sending SMS to {$number} with content: {$content}", ['tag' => $tag]);
        }

        $method_url = $this->url;

        $data = [
            'type' => 'transactional',
            'unicodeEnabled' => false,
            'sender' => $this->sms_sender_name,
            'recipient' => str_replace(' ', '', $number),
            'content' => $content,
            'webUrl' => $this->sms_webhook,
        ];

        if ($tag) {
            $data['tag'] = $tag;
        }
        try {
            $res = \Http::withHeaders($this->api_headers)->post($method_url, $data);

            if (isset($res->remaining_credit, $this->setting_sms_counter_column_name)) {
                DB::table($this->setting_table_name)
                    ->where("{$this->setting_column_name}", "{$this->setting_sms_counter_column_name}")
                    ->update([
                        "{$this->setting_sms_counter_value_name}" => $res->remaining_credit,
                    ]);
            }

            return $res->object();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
