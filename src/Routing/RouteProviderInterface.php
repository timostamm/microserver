<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 02.05.18
 * Time: 13:23
 */

namespace TS\Web\Microserver\Routing;

use Symfony\Component\Routing\RouteCollection;

interface RouteProviderInterface
{
    public function getRoutes(RouteCollection $routes): void;
}