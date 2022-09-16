<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HelloController
{
    /**
     * HelloController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $body = "App running 🚀";
        $response->getBody()->write($body);
        return $response;
    }
}
