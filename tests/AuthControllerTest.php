<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\User\User;
use Doctrine\ORM\EntityManager;

/**
 * Class HelloTest
 * @package Tests
 */
class AuthControllerTest extends BaseTestCase
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

    public function testLoginEndpoint()
    {
        $user = new User('John Doe', 'test@test.com','12345678');
        $em = $this->app->getContainer()->get(EntityManager::class);
        $em->persist($user);
        $em->flush();

        $payload = [
            'email' => $user->email,
            'password' => '12345678'
        ];

        $request = $this->createRequest('POST', '/login');
        $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode($payload));

        // Act
        $response = $this->app->handle($request);

        $code = $response->getStatusCode();

        // Assert
        $this->assertEquals(200, $code);
    }

    public function testLoginWithWrongInputEndpoint()
    {

        $payload = [
            'email' => 'emailthat@doesnotexists.com',
            'password' => '12345678'
        ];

        $request = $this->createRequest('POST', '/login');
        $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode($payload));

        // Act
        $response = $this->app->handle($request);

        $code = $response->getStatusCode();

        // Assert
        $this->assertEquals(401, $code);
    }
}
