<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 21:33
 */

namespace TS\Web\Microserver\Routing;


use TS\DependencyInjection\Reflection\Reflector;


abstract class ParameterConverter
{

    public function convert(string $name, $value, ?string $requiredType)
    {
        if (is_null($requiredType)) {
            return $value;
        }
        if (Reflector::isBuiltinType($requiredType)) {
            return $this->convertBuiltinType($name, $value, $requiredType);
        } else {
            return $this->convertClassType($name, $value, $requiredType);
        }
    }


    abstract protected function convertBuiltinType(string $name, $value, string $type);


    abstract protected function convertClassType(string $name, $value, string $class);


}