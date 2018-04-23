<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use TS\Web\Microserver\SimpleServer;
use TS\Web\Microserver\Simple\SimpleController;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);


$request = Request::createFromGlobals();
$server = new SimpleServer();
$server->addController(SimpleController::class);
$response = $server->serve($request);
$response->send();
