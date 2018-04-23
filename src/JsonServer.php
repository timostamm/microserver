<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 23:04
 */

namespace TS\Web\Microserver;


use TS\DependencyInjection\Injector;
use TS\Web\Microserver\Routing\ParameterConverter;
use TS\Web\Microserver\Routing\SimpleParameterConverter;

class JsonServer extends Server
{

    protected function configure(Injector $injector): void
    {
        $injector->alias(ParameterConverter::class, SimpleParameterConverter::class);

        //      $injector->singleton(ParameterConverter::class);

        //DateTime::createFromFormat

    }


}