<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$ENV = $_ENV['ENV'] ?? 'dev';
$containerBuilder = new ContainerBuilder();

// Import services
$dependencies = require __DIR__ . '/../app/services.php';
$dependencies($containerBuilder);

// Initialize app with PHP-DI
$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();
// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$displayErrorDetails = $ENV == 'dev';
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable              $exception,
) use ($app) {
    $payload = ['error' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );

    return $response->withStatus($exception->getCode())
        ->withHeader('Content-Type', 'application/json');
};
// Error Handler
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

$app->run();
