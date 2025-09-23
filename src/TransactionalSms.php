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
            } else if (isset($this->setting_sms_counter_column_name)) {
                $url = $this->api_base_url . 'account';


                if (config('brevo.LOG_ENABLED')) {
                    Log::info("Fetching SMS credits to update the counter.", [
                        'url' => $url,
                        'headers' => $this->api_headers,
                    ]);
                }

                try {
                    $res = \Http::withHeaders($this->api_headers)->post($url);
                } catch (\Exception $e) {
                    if (config('brevo.LOG_ENABLED')) {
                        Log::error("Error fetching account details", ['error' => $e->getMessage()]);
                    }
                    return $e->getMessage();
                }

                $response = $res->object();

                if (config('brevo.LOG_ENABLED')) {
                    Log::info("Account response", ['response' => $response]);
                }
                if ($response->plan) {
                    foreach ($response->plan as $plan) {

                        if (config('brevo.LOG_ENABLED')) {
                            Log::info("Plan details", ['plan' => $plan]);
                        }

                        if ($plan['type'] === 'sms') {
                            $res = DB::table($this->setting_table_name)
                                ->where("{$this->setting_column_name}", "{$this->setting_sms_counter_column_name}")
                                ->update([
                                    "{$this->setting_sms_counter_value_name}" => $plan['credits'],
                                ]);

                            if (config('brevo.LOG_ENABLED')) {
                                Log::info("SMS credits updated in the database.", ['credits' => $plan['credits']]);
                            }
                        }
                    }
                }
            }

            return $res->object();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
