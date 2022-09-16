<?php

declare(strict_types=1);

namespace Tests;

/**
 * Class HelloTest
 * @package Tests
 */
class UserControllerTest extends BaseTestCase
{
    /**
     * @var \Slim\App
     */
    protected $app;


    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app = $this->getAppInstance();
    }

    public function testUserCreateEndpoint()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => '12345678'
        ];

        $request = $this->createRequest('POST', '/users');
        $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode($payload));

        // Act
        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        // Assert
        $this->assertEquals(201, $code);
    }

    public function testUserCreateWithIncompleteInformation()
    {
        $payload = [
            'email' => 'john@doe.com',
            'password' => '12345678'
        ];

        $request = $this->createRequest('POST', '/users');
        $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode($payload));

        // Act
        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        // Assert
        $this->assertEquals(422, $code);
    }
}
