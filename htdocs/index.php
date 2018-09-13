<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use dummy\Simple\ControllerWithDependency;
use dummy\Simple\SimpleController;
use TS\Web\Microserver\Server;


/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);
$loader->addPsr4('dummy\\', __DIR__ . '/dummy');


$server = new Server(true);
$server->addController(SimpleController::class);
$server->addController(ControllerWithDependency::class, function () {
    return new ControllerWithDependency('dependency');
});
$server->serve()->send();
