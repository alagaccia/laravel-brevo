<?php

namespace Alagaccia\Brevo;

use Alagaccia\Brevo\Brevo;
use Illuminate\Support\Facades\DB;

class TransactionalEmail extends Brevo
{
    protected $url;
    protected $url_get_emails;
    protected $url_template;
    protected $url_blockedContacts;

    public function __construct()
    {
        parent::__construct();

        $this->url = $this->api_base_url . 'smtp/email';
        $this->url_get_emails = $this->api_base_url . 'smtp/emails';
        $this->url_template = $this->api_base_url . 'smtp/templates';
        $this->url_blockedContacts = $this->api_base_url . 'smtp/blockedContacts';
    }

    /*
     * method: POST
     */
    public function send($to, $templateId = null, $params = null, $tags = null)
    {
        $res = \Http::withHeaders($this->api_headers)->post($this->url, [
            'to' => [
                [
                    'email' => $to['email'],
                    'name' => $to['name'] ?? null,
                ]
            ],
            'templateId' => $templateId ?? null,
            'params' => $params,
            'tags' => $tags,
        ]);

        return $res->object();
    }

    /*
     * method: GET
     * return: { count: x, transactionalEmails: {email, subject, messageId, uuid, date, templateId, from, tags[]} }
     */
    public function getEmails($params)
    {
        try {
            $res = \Http::withHeaders($this->api_headers)->get($this->url_get_emails, $params);
            return $res->object();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $res->object();
    }

    /*
     * method: GET
     * return: { email, subject, date, events[], body, attachmentCount, templateId }
     */
    public function getEmail($uuid)
    {
        $res = \Http::withHeaders($this->api_headers)->get($this->url_get_emails . '/' . $uuid);

        return $res->object();
    }

    /*
     * method: GET
     * return: { list }
     */
    public function isTransactionalEmailBlocked(string $email): bool
    {
        $limit = 50; // Imposta il limite massimo per pagina
        $offset = 0;
        $found = false;

        try {
            do {
                $response = \Http::withHeaders($this->api_headers)->get($this->url_blockedContacts, [
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $contacts = $data['contacts'] ?? [];

                    foreach ($contacts as $contact) {
                        if (($contact['email'] ?? null) === $email) {
                            $found = true;
                            break 2; // Esci dai cicli foreach e do-while
                        }
                    }

                    $offset += $limit; // Prepara l'offset per la prossima pagina

                } else {
                    throw new \Exception('Brevo API error checking transactional blocked contacts (page ' . ($offset / $limit + 1) . '): ' . $response->body());
                    // In caso di errore API, potresti decidere di assumere che non sia bloccato
                    // o di lanciare un'eccezione, a seconda della tua politica di gestione errori.
                    return false; // Esci e indica che non è stato trovato (o gestisci l'errore diversamente)
                }
            } while (count($contacts) === $limit); // Continua finché ci sono esattamente 'limit' contatti, suggerendo che ci potrebbero essere altre pagine

            return $found;
        } catch (\Exception $e) {
            throw new \Exception('Exception during Brevo transactional blacklist check: ' . $e->getMessage());
            return false;
        }
    }
}
