<?php

namespace App\Controllers;

use App\Domain\Auth\Service\AuthenticateUser;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function __construct(private AuthenticateUser $authenticateUser)
    {
    }

    public function login(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody(), true);

        try {
            $auth = $this->authenticateUser->validateUser($data);
        } catch (ValidationException $e) {
            $response->getBody()->write((string)json_encode($e->getErrors()));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        $result = ['token' => $auth];
        $response->getBody()->write(json_encode($result));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
