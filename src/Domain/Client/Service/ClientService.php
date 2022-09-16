<?php

namespace App\Domain\Client\Service;

class ClientService
{

    private string $apiUrl;
    private string $endPoint;
    private array $headers;
    private mixed $callback;

    public function __construct()
    {
        $this->apiUrl = 'https://stooq.com/q/l';
        $this->headers = [
            'Content-Type: application/json',
        ];
    }

    public function getStockInformation($name)
    {
        $this->endPoint = "/?s={$name}&f=sd2t2ohlcvn&e=json";
        $this->get();
        return $this->callback->symbols[0];
    }

    private function get()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . $this->endPoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $this->headers,
        ]);

        $this->callback = json_decode(curl_exec($curl));
        curl_close($curl);
    }
}
