<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 18.04.18
 * Time: 15:00
 *
 * This file was copied and modified from the Symfony package.
 * License: MIT
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 */

namespace TS\Web\Microserver\Controller;


use Symfony\Component\HttpFoundation\Request;


abstract class AbstractControllerResolver implements ControllerResolverInterface
{

    public function getController(Request $request): callable
    {
        if (!$controller = $request->attributes->get('_controller')) {
            throw new \InvalidArgumentException(sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }
        if (is_array($controller) && count($controller) === 2 && is_object($controller[0]) && is_callable($controller)) {
            return $controller;
        }

        // we need to get / create an instance of the controller

        if (is_string($controller)) {
            $a = explode('::', $controller);
            $classname = $a[0];
            $methodname = $a[1];
        } else if (is_array($controller) && count($controller) === 2 && is_string($controller[0]) && is_string($controller[1])) {
            $classname = $controller[0];
            $methodname = $controller[1];
        } else {
            throw new \InvalidArgumentException(sprintf('The controller for URI "%s" is not callable.', $request->getPathInfo() ));
        }

        $instance = $this->instantiateController($classname);

        return [$instance, $methodname];
    }


    protected function instantiateController(string $classname)
    {
        return new $classname();
    }


}