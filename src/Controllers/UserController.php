<?php


namespace App\Controllers;

use App\Domain\User\Service\UserCreate;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{

    public function __construct(private UserCreate $userCreate)
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody(), true);

        try {
            $userId = $this->userCreate->createUser($data);
        } catch (ValidationException $e) {
            $response->getBody()->write((string)json_encode($e->getErrors()));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $result = [
            'userId' => $userId
        ];
        $response->getBody()->write((string)json_encode($result));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
}
