<?php

namespace App\Domain\Stock\Service;

use App\Domain\Client\Service\ClientService;

class StockInformationService
{
    public function __construct(private ClientService $client)
    {
    }

    public function getStock($name)
    {
        return $this->client->getStockInformation($name);
    }
}
