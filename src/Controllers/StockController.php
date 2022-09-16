<?php

namespace App\Controllers;

use App\Domain\Log\Repository\LogRepository;
use App\Domain\Log\Service\LogCreate;
use App\Domain\Queue\Service\Producer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Stock\Service\StockInformationService;

class StockController
{

    public function __construct(
        private StockInformationService $stockInformation,
        private LogCreate               $logCreate,
        private LogRepository           $logRepository,
        private Producer                $producer,
    )
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function find(Request $request, Response $response): Response
    {
        $data = $request->getQueryParams();

        if (!array_key_exists('q', $data)) {
            $response->getBody()->write(json_encode(['message' => 'Stock code not provided']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $result = $this->stockInformation->getStock($data['q']);

        if (!property_exists($result, 'name')) {
            $response->getBody()->write(json_encode(['message' => 'Wrong stock code provided']));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $normalizedResponse = $this->normalizeStockResponse($result);

        $token = $request->getAttribute("token");

        $this->logCreate->createLog(json_encode($normalizedResponse), $token['id']);
        $this->producer->produce($token['email'], $normalizedResponse);

        $response->getBody()->write((string)json_encode($normalizedResponse));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function history(Request $request, Response $response)
    {
        $token = $request->getAttribute("token");

        $result = $this->logRepository->findLogsByUserId($token['id']);

        if (!$result) {
            $response->getBody()->write(json_encode(['message' => 'History not found for provied user']));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $normalizedResult = $this->normalizeHistoryResponse($result);
        $response->getBody()->write((string)json_encode($normalizedResult));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    }

    public function normalizeHistoryResponse($result)
    {
        $normalizedResult = [];

        foreach ($result as $value) {
            $normalizedResultValues = new \StdClass;
            $normalizedResultValues->date = $value['date']->format('Y-m-d\TH:i:s\Z');
            foreach (json_decode($value['result']) as $index => $item) {
                $normalizedResultValues->{$index} = $item;
            }
            $normalizedResult[] = $normalizedResultValues;
        }

        return $normalizedResult;
    }

    private function normalizeStockResponse($result)
    {
        $properties = [
            'name',
            'symbol',
            'open',
            'high',
            'low',
            'close'
        ];

        $normalizedResult = new \StdClass;

        foreach ($properties as $property) {
            $normalizedResult->{$property} = $result->{$property};
        }

        return $normalizedResult;
    }
}
