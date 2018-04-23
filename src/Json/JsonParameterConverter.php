<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:46
 */

namespace TS\Web\Microserver\Json;


use TS\Web\Microserver\Routing\ParameterConverter;


class JsonParameterConverter extends ParameterConverter
{

    protected function convertBuiltinType(string $name, $value, string $type)
    {
        switch ($type) {
            case 'bool':
                if ($value === 'true') {
                    $value = true;
                }
                if ($value === 'false') {
                    $value = false;
                }
                break;
            case 'float':
            case 'int':
                if (is_numeric($value)) {
                    settype($value, $type);
                }
        }
        return $value;
    }

    protected function convertClassType(string $name, $value, string $class)
    {
        return $value;
    }


}