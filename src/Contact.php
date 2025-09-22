<?php

namespace AndreaLagaccia\Brevo;

use Illuminate\Support\Facades\Http;
use AndreaLagaccia\Brevo\Brevo;

class Contact extends Brevo
{
    protected $url;

    public function __construct()
    {
        parent::__construct();

        $this->url = $this->api_base_url . 'contacts/';
    }

    public function create($email, $ATTRIBUTES, $list_ids = null)
    {
        $method_url = $this->url;
        $listIds = !empty($list_ids) ? implode(',', $list_ids) : $this->list_id;

        $res = Http::withHeaders($this->api_headers)->post($method_url, [
            'updateEnabled' => false,
            'listIds' => [(int) $listIds],
            'email' => $email,
            'attributes' => $ATTRIBUTES,
        ]);

        return $res;
    }

    public function updateOrCreate($email, $ATTRIBUTES, $list_ids = null)
    {
        $method_url = $this->url;
        $listIds = !empty($list_ids) ? implode(',', $list_ids) : $this->list_id;

        $res = Http::withHeaders($this->api_headers)->post($method_url, [
            'updateEnabled' => true,
            'listIds' => [(int) $listIds],
            'email' => $email,
            'attributes' => $ATTRIBUTES,
        ]);

        return $res->body();
    }

    public function update($email, $ATTRIBUTES, $list_ids = null)
    {
        $method_url = $this->url . urlencode($email);
        $listIds = !empty($list_ids) ? implode(',', $list_ids) : $this->list_id;

        $res = Http::withHeaders($this->api_headers)->put($method_url, [
            'listIds' => [(int) $listIds],
            'attributes' => $ATTRIBUTES,
        ]);

        return $res->body();
    }

    public function delete($email)
    {
        $method_url = $this->url . urlencode($email);

        $res = Http::withHeaders($this->api_headers)->delete($method_url);

        return $res->body();
    }

    public function getInfo($email)
    {
        $method_url = $this->url . urlencode($email);

        return Http::withHeaders($this->api_headers)->get($method_url);
    }

    /*
     * @customContactFilter     object              REQUIRED (Set the filter for the contacts to be exported.)
     * ref:                     https://developers.brevo.com/reference/requestcontactexport-1
     */
    public function export($customContactFilter = [])
    {
        $method_url = $this->url . 'export';

        $res = Http::withHeaders($this->api_headers)->post($method_url, [
            'customContactFilter' => $customContactFilter,
        ]);

        return $res;
    }
}
