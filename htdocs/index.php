<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use TS\Web\Microserver\Server;
use TS\Web\Microserver\Simple\ControllerWithDependency;
use TS\Web\Microserver\Simple\SimpleController;


/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);


$server = new Server(true);
$server->addController(SimpleController::class);
$server->addController(ControllerWithDependency::class, function () {
    return new ControllerWithDependency('dependency');
});
$server->serve()->send();
