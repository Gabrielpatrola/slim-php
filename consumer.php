<?php
require __DIR__ . '/vendor/autoload.php';

use App\Domain\Queue\Service\Consumer;
use DI\ContainerBuilder;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;
use App\Domain\Mail\Service\MailService;


$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$containerBuilder = new ContainerBuilder();
$services = require_once __DIR__ . '/app/services.php';
$services($containerBuilder);

$container = $containerBuilder->build();
$mailService = new MailService($container->get('Swift_Mailer'));

$output =  new ConsoleOutput();


$consumer = new Consumer($container->get('PhpAmqpLib\Channel\AMQPChannel'), $mailService, $output);
$consumer->listen();
