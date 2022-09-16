<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([

        Swift_Mailer::class => function () {
            $host = $_ENV['MAILER_HOST'] ?? 'smtp.mailtrap.io';
            $port = intval($_ENV['MAILER_PORT']) ?? 465;
            $username = $_ENV['MAILER_USERNAME'] ?? 'test';
            $password = $_ENV['MAILER_PASSWORD'] ?? 'test';

            $transport = (new Swift_SmtpTransport($host, $port))
                ->setUsername($username)
                ->setPassword($password);

            return new Swift_Mailer($transport);
        },

        EntityManager::class => function (): EntityManager {
            $doctrine = [
                'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => $_ENV['DB_PORT'] ?? 3306,
                'dbname' => $_ENV['DB_NAME'] ?? 'mydb',
                'user' => $_ENV['DB_USER'] ?? 'user',
                'password' => $_ENV['DB_PASSWORD'] ?? 'secret',
                'charset' => 'utf8'
            ];

            $devMode = $_ENV['ENV'] !== 'PROD';
            $cacheDirectory = __DIR__ . '/../var/doctrine';
            $metadataDirectories = [__DIR__ . '/../src/Domain'];

            $cache = $devMode ?
                DoctrineProvider::wrap(new ArrayAdapter()) :
                DoctrineProvider::wrap(new FilesystemAdapter(directory: $cacheDirectory));

            $config = Setup::createAttributeMetadataConfiguration(
                $metadataDirectories,
                $devMode,
                null,
                $cache
            );

            return EntityManager::create($doctrine, $config);
        },

        AMQPChannel::class => function () {
            $connection = new AMQPStreamConnection(
                $_ENV['RMQ_HOST'],
                $_ENV['RMQ_PORT'],
                $_ENV['RMQ_USERNAME'],
                $_ENV['RMQ_PASSWORD'],
                $_ENV['RMQ_VHOST']);

            return $connection->channel();
        }
    ]);

};
