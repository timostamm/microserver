<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 02.05.18
 * Time: 17:13
 */

namespace TS\Web\Microserver\Exception;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class RoutingExceptionHandler extends ExceptionHandler
{
    public function handleException(\Exception $exception, Request $request): ?Response
    {
        if ($exception instanceof NoConfigurationException) {
            $msg = sprintf('No routes configured.');
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $msg, $exception);
        }
        if ($exception instanceof MethodNotAllowedException) {
            $msg = sprintf('Method %s is not allowed.', $request->getMethod());
            throw new HttpException(Response::HTTP_METHOD_NOT_ALLOWED, $msg, $exception);
        }
        if ($exception instanceof ResourceNotFoundException) {
            $msg = sprintf('Not found.');
            throw new HttpException(Response::HTTP_NOT_FOUND, $msg, $exception);
        }

        return null;
    }

}