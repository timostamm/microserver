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


class SimpleControllerResolver extends AbstractControllerResolver
{

    protected $controllerInstances = [];


    public function setControllerInstance(string $classname, $instance): void
    {
        $this->controllerInstances[$classname] = $instance;
    }

    protected function instantiateController(string $classname)
    {

        if (isset($this->controllerInstances[$classname])) {
            return $this->controllerInstances[$classname];
        }

        return new $classname();
    }


}