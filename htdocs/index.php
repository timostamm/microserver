<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use TS\Web\Microserver\Server;
use TS\Web\Microserver\Normal\NormalController;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);


$request = Request::createFromGlobals();
$server = new Server();
$server->addController(NormalController::class);
$response = $server->serve($request);
$response->send();
