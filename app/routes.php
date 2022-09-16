<?php

declare(strict_types=1);

use App\Controllers\HelloController;
use App\Controllers\StockController;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Domain\User\User;
use Doctrine\ORM\EntityManager;
use Slim\Exception\HttpForbiddenException;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;

return function (App $app) {
    $app->get('/', HelloController::class . ':index');

    /** Authentication route */
    $app->post('/login', AuthController::class . ':login');

    /** Users Routes */
    $app->post('/users', UserController::class . ':store');

    /** Authenticated Routes */
    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/stock', StockController::class . ':find');
        $group->get('/history', StockController::class . ':history');
    })->add(new Tuupola\Middleware\JwtAuthentication([
        "secret" => $_ENV['JWT_SECRET'],
        "before" => function ($request, $arguments) use ($app) {
            $token = $request->getAttribute("token");
            $now = time();

            if ($now > $token['exp']) {
                throw new HttpForbiddenException($request, 'Token expired or invalid');
            }

            $user = $app->getContainer()->get(EntityManager::class)->getRepository(User::class)->findOneBy(array('id' => $token['id']));
            if (!$user) {
                throw new HttpForbiddenException($request, 'Token expired or invalid');
            }
        },
        "error" => function ($response, $arguments) {
            $data["message"] = $arguments["message"];
            $response->getBody()->write(
                json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );
            return $response->withHeader("Content-Type", "application/json")
                ->withStatus(401);
        }
    ]));
};
