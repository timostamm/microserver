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


class ControllerResolver extends AbstractControllerResolver
{

    protected $controllerInstances = [];
    protected $controllerFactories = [];


    public function setControllerInstance(string $classname, $instance): void
    {
        $this->controllerInstances[$classname] = $instance;
    }

    public function setControllerFactory(string $classname, callable $factory): void
    {
        $this->controllerFactories[$classname] = $factory;
    }

    protected function instantiateController(string $classname)
    {
        if (isset($this->controllerInstances[$classname])) {
            return $this->controllerInstances[$classname];
        }

        if (isset($this->controllerFactories[$classname])) {
            $factory = $this->controllerFactories[$classname];
            try {
                $instance = $factory();
                $this->controllerInstances[$classname] = $instance;
                return $instance;
            } catch (\Throwable $throwable) {
                $msg = sprintf('Factory for controller %s failed: %s', $classname, $throwable->getMessage());
                throw new \LogicException($msg, 0, $throwable);
            }
        }

        return new $classname();
    }


}