<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 02.05.18
 * Time: 13:36
 */

namespace TS\Web\Microserver\Controller;


use TS\Web\Microserver\Routing\RouteProvider;

class ControllerConfig {

    private $routeProvider;
    private $controllerResolver;

    public function __construct(RouteProvider $routeProvider, DIControllerResolver $controllerResolver)
    {
        $this->routeProvider = $routeProvider;
        $this->controllerResolver = $controllerResolver;
    }

    public function addControllerClass(string $classname):void
    {
        $this->routeProvider->addControllerClass($classname);
    }

    public function addControllerInstance($controllerObj):void
    {
        $this->routeProvider->addControllerClass(get_class($controllerObj));
        $this->controllerResolver->setControllerInstance(get_class($controllerObj), $controllerObj);
    }
}
