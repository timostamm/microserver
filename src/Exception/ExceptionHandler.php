<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 23.04.18
 * Time: 22:22
 */

namespace TS\Web\Microserver\Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ExceptionHandler
{

    /**
     * Handles an exception that occurred while serving a request.
     *
     * The method may return NULL if it cannot handle the exception.
     * Or throw a HttpException.
     * Or return a Response.
     *
     * @param \Exception $ex
     * @param Request $request
     * @return null|Response
     */
    abstract public function handleException(\Exception $ex, Request $request): ?Response;


}