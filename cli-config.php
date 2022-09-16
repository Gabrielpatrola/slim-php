<?php

// cli-config.php
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use DI\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$containerBuilder = new ContainerBuilder();
$services = require_once __DIR__ . '/app/services.php';
$services($containerBuilder);

$container = $containerBuilder->build();
return ConsoleRunner::createHelperSet($container->get(EntityManager::class));
