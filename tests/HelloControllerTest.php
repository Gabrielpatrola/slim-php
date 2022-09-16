<?php

declare(strict_types=1);

namespace Tests;

/**
 * Class HelloTest
 * @package Tests
 */
class HelloControllerTest extends BaseTestCase
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

    public function testHelloEndpoint()
    {
        // Arrange
        $request = $this->createRequest('GET', '/');

        // Act
        $response = $this->app->handle($request);
        $body = (string) $response->getBody();

        // Assert
        $this->assertEquals("App running 🚀", $body);
    }
}
