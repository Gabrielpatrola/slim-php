<?php

declare(strict_types=1);

namespace Tests;

use DI\ContainerBuilder;
use Exception;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;
use Symfony\Component\Dotenv\Dotenv;

class BaseTestCase extends PHPUnit_TestCase
{
    /**
     * @return App
     * @throws Exception
     */
    protected function getAppInstance(): App
    {
        parent::setUp();

        $containerBuilder = new ContainerBuilder();

        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env');

        $dependencies = require __DIR__ . '/../app/services.php';
        $dependencies($containerBuilder);

        $container = $containerBuilder->build();
        AppFactory::setContainer($container);

        $app = AppFactory::create();

        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);

        return $app;
    }

    /**
     * @return String
     */
    protected function getAuthorizationHeader($id = 1, $name = 'John Doe', $email = 'john@doe.com'): string
    {
        $now = time();
        $future = strtotime('+1 hour', $now);

        return JWT::encode([
            'id' => $id,
            'username' => $name,
            'email' => $email,
            "iat" => $now,
            "exp" => $future
        ],
            $_ENV['JWT_SECRET'],
            'HS256');
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $headers
     * @param array $cookies
     * @param array $serverParams
     * @return Request
     */
    protected function createRequest(
        string $method,
        string $path,
        array  $headers = ['HTTP_ACCEPT' => 'application/json'],
        string $query = '',
        array  $cookies = [],
        array  $serverParams = []
    ): Request
    {
        $uri = new Uri('', 'localhost', 80, $path, $query);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }

    public static function setUpBeforeClass(): void
    {
        shell_exec('php vendor/bin/doctrine orm:schema-tool:drop --force');

        shell_exec('php vendor/bin/doctrine orm:schema-tool:update --force');
    }
}
