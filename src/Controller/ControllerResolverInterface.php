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


interface ControllerResolverInterface
{

    /**
     * Finds the controller that is responsible for the current request.
     */
    public function getController(Request $request): callable;


}