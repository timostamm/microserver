<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Request;
use TS\Web\Microserver\Server;
use TS\Web\Microserver\Normal\NormalController;
use TS\Web\Microserver\Controller\ControllerConfig;
use TS\DependencyInjection\Injector;


/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);


$request = Request::createFromGlobals();

$server = new class extends Server {

    protected function configure(Injector $injector, ControllerConfig $controllerConfig): void
    {
        $controllerConfig->addControllerClass(NormalController::class);
    }

};

$response = $server->serve($request);
$response->send();
