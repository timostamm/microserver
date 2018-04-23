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


use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TS\Web\Microserver\HttpException;


interface ControllerInvokerInterface
{


    /**
     * Calls the controller, which should return a Response.
     *
     * @throws LogicException when the controller did not return a Response
     * @throws HttpException
     */
    public function invoke(callable $controller, Request $request, Response $response): Response;


}