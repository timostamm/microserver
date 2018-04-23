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




use TS\DependencyInjection\InjectorInterface;

class DIControllerResolver extends SimpleControllerResolver
{

    protected $injector;

    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
    }


    protected function instantiateController(string $classname)
    {

        if (isset($this->controllerInstances[$classname])) {
            return $this->controllerInstances[$classname];
        }
        return $this->injector->instantiate($classname);
    }


}