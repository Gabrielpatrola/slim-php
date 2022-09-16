<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\User\User;
use Doctrine\ORM\EntityManager;

/**
 * Class HelloTest
 * @package Tests
 */
class StockControllerTest extends BaseTestCase
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

    public function testStockEndpoint()
    {
        $user = new User('John Doe', 'john@doe.com', '12345678');
        $em = $this->app->getContainer()->get(EntityManager::class);
        $em->persist($user);
        $em->flush();

        $request = $this->createRequest('GET', '/stock', [
            'Authorization' => "Bearer {$this->getAuthorizationHeader()}"
        ], 'q=googl.us');

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(200, $code);
    }

    public function testStockWithoutAuthentication()
    {
        $request = $this->createRequest('GET', '/stock', [], 'q=googl.us');

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(401, $code);
    }

    public function testStockWithoutInput()
    {
        $request = $this->createRequest('GET', '/stock', [
            'Authorization' => "Bearer {$this->getAuthorizationHeader()}"
        ]);

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(422, $code);
    }

    public function testStockWithWrongInput()
    {
        $request = $this->createRequest('GET', '/stock', [
            'Authorization' => "Bearer {$this->getAuthorizationHeader()}"
        ], 'q=goog');

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(404, $code);
    }

    public function testHistoryEndpoint()
    {
        $request = $this->createRequest('GET', '/stock', [
            'Authorization' => "Bearer {$this->getAuthorizationHeader()}"
        ], 'q=googl.us');

        $this->app->handle($request);

        $request = $this->createRequest('GET', '/history', [
            'Authorization' => "Bearer {$this->getAuthorizationHeader()}"
        ]);

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(200, $code);
    }

    public function testHistoryWithoutAuthentication()
    {
        $request = $this->createRequest('GET', '/history');

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(401, $code);
    }

    public function testHistoryWithoutResults()
    {
        $user = new User('John Doe 2', 'test2@test.com', '12345678');
        $em = $this->app->getContainer()->get(EntityManager::class);
        $em->persist($user);
        $em->flush();

        $request = $this->createRequest('GET', '/history', [
            'Authorization' => "Bearer {$this->getAuthorizationHeader($user->id, $user->name, $user->email)}"
        ]);

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(404, $code);
    }
}
