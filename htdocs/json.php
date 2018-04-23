<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use TS\Web\Microserver\JsonServer;
use TS\Web\Microserver\Json\JsonController;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);


$request = Request::createFromGlobals();
$server = new JsonServer();
$server->addController(JsonController::class);
$response = $server->serve($request);
$response->send();
